<?php

namespace App\Http\Controllers;

use App\Models\CompraTienda;
use App\Models\Inventario;
use App\Models\PackPuntos;
use App\Models\Recompensa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TiendaController extends Controller
{
    private function assertProductoTienda(Recompensa $recompensa): Recompensa
    {
        abort_unless($recompensa->tipo === 'tienda', 404);

        return $recompensa;
    }

    public function index(): View
    {
        $articulos = Recompensa::query()
            ->where('tipo', 'tienda')
            ->orderBy('puntos_necesarios')
            ->get();

        return view('tienda.index', [
            'articulos' => $articulos,
        ]);
    }

    public function articulo(Recompensa $recompensa): View
    {
        $articulo = $this->assertProductoTienda($recompensa);

        return view('tienda.articulo', [
            'articulo' => $articulo,
        ]);
    }

    public function confirmacion(Recompensa $recompensa): View
    {
        $articulo = $this->assertProductoTienda($recompensa);

        return view('tienda.confirmacion', [
            'articulo' => $articulo,
        ]);
    }

    public function comprar(Recompensa $recompensa): RedirectResponse
    {
        $articulo = $this->assertProductoTienda($recompensa);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $coste = (int) $articulo->puntos_necesarios;

        if ((int) $user->puntos < $coste) {
            return redirect()->route('tienda.confirmacion', ['recompensa' => $articulo->id])
                ->with('status', 'No tienes puntos suficientes para comprar este articulo.');
        }

        DB::transaction(function () use ($user, $articulo, $coste) {
            $user->puntos = (int) $user->puntos - $coste;
            $user->save();

            CompraTienda::create([
                'user_id' => $user->id,
                'recompensa_id' => $articulo->id,
                'puntos_gastados' => $coste,
            ]);

            Inventario::create([
                'user_id' => $user->id,
                'recompensa_id' => $articulo->id,
                'origen' => 'tienda',
                'obtenida_at' => now(),
            ]);
        });

        return redirect()->route('tienda.compra', ['recompensa' => $articulo->id])
            ->with('status', 'Articulo comprado correctamente.');
    }

    public function compra(Recompensa $recompensa): View
    {
        $articulo = $this->assertProductoTienda($recompensa);

        return view('tienda.compra', [
            'articulo' => $articulo,
        ]);
    }

    public function puntos(): View
    {
        $packs = PackPuntos::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        return view('tienda.puntos', [
            'packs' => $packs,
        ]);
    }

    public function confirmacionPuntos(PackPuntos $packPuntos): View
    {
        abort_unless($packPuntos->activo, 404);

        return view('tienda.confirmacion_puntos', [
            'pack' => $packPuntos,
        ]);
    }

    public function comprarPuntos(PackPuntos $packPuntos): RedirectResponse
    {
        abort_unless($packPuntos->activo, 404);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $user->puntos = (int) $user->puntos + (int) $packPuntos->puntos;
        $user->save();

        return redirect()->route('tienda.puntos.compra', ['packPuntos' => $packPuntos->id])
            ->with('status', "Has comprado {$packPuntos->puntos} puntos correctamente.");
    }

    public function compraPuntos(PackPuntos $packPuntos): View
    {
        abort_unless($packPuntos->activo, 404);

        return view('tienda.compra_puntos', [
            'pack' => $packPuntos,
        ]);
    }
}
