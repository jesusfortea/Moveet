<?php

namespace App\Http\Controllers;

use App\Models\CompraTienda;
use App\Models\Inventario;
use App\Models\PackPuntos;
use App\Models\Recompensa;
use Illuminate\Http\RedirectResponse;
use App\Models\Factura;
use App\Models\User;
use App\Mail\FacturaPagoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\NotificationService;
use App\Services\PointsHistoryService;

class TiendaController extends Controller
{
    public function __construct(
        private PointsHistoryService $pointsHistoryService,
        private NotificationService $notificationService,
    ) {
    }

    private function assertProductoTienda(Recompensa $recompensa): Recompensa
    {
        abort_unless($recompensa->tipo === 'tienda' && $recompensa->visible_en_tienda, 404);

        return $recompensa;
    }

    public function index(): View
    {
        $user = Auth::user();

        $articulos = Recompensa::query()
            ->where('tipo', 'tienda')
            ->where('visible_en_tienda', true)
            ->orderBy('puntos_necesarios')
            ->get();

        return view('tienda.index', [
            'articulos' => $articulos,
            'esPremium' => (bool) ($user?->premium),
        ]);
    }

    public function articulo(Recompensa $recompensa): View
    {
        $articulo = $this->assertProductoTienda($recompensa);
        $user = Auth::user();

        return view('tienda.articulo', [
            'articulo' => $articulo,
            'esPremium' => (bool) ($user?->premium),
        ]);
    }

    public function confirmacion(Recompensa $recompensa): View
    {
        $articulo = $this->assertProductoTienda($recompensa);
        $user = Auth::user();

        return view('tienda.confirmacion', [
            'articulo' => $articulo,
            'esPremium' => (bool) ($user?->premium),
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

        if ($articulo->premium && !(bool) $user->premium) {
            return redirect()->route('tienda.confirmacion', ['recompensa' => $articulo->id])
                ->with('status', 'Este articulo es premium. Necesitas pase de pago para comprarlo.');
        }

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

        $this->pointsHistoryService->log(
            $user,
            'spent',
            -$coste,
            'Compra en tienda: ' . $articulo->nombre
        );

        $this->notificationService->notify(
            $user->id,
            'store',
            'Compra completada',
            'Has comprado "' . $articulo->nombre . '" correctamente.',
            route('usuario.inventario')
        );

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

        $this->pointsHistoryService->log(
            $user,
            'earned',
            (int) $packPuntos->puntos,
            'Compra de pack de puntos: ' . $packPuntos->nombre
        );

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

    public function capturarPayPalPuntos(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No autorizado'], 401);
        }

        $packId = $request->input('pack_id');
        $pack = PackPuntos::findOrFail($packId);

        // 1. Crear factura
        $factura = Factura::create([
            'user_id'         => $user->id,
            'importe'         => $pack->precio_euros,
            'concepto'        => "Compra de puntos: {$pack->nombre}",
            'nombre_titular'  => $user->name,
            'email_titular'   => $user->email,
            'ultimos_digitos' => 'PAYP',
        ]);

        // 2. Añadir puntos
        $user->puntos = (int) $user->puntos + (int) $pack->puntos;
        $user->save();

        $this->pointsHistoryService->log(
            $user,
            'earned',
            (int) $pack->puntos,
            'Compra PayPal de pack de puntos: ' . $pack->nombre
        );

        $this->notificationService->notify(
            $user->id,
            'billing',
            'Puntos añadidos',
            'Tu compra de puntos se ha procesado correctamente.',
            route('usuario.historial_puntos')
        );

        // 3. Generar PDF y enviar correo
        $pdf = Pdf::loadView('pdf.factura', [
            'factura' => $factura,
            'user'    => $user,
        ]);

        try {
            Mail::to($user->email)->send(new FacturaPagoMail($factura, $pdf->output()));
        } catch (\Exception $e) {
            \Log::error('Error enviando email de factura (puntos): ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Puntos comprados con éxito',
            'redirect' => route('pago.exito', ['factura' => $factura->id])
        ]);
    }

    public function capturarPayPalArticulo(Request $request, Recompensa $recompensa)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No autorizado'], 401);
        }

        $articulo = $this->assertProductoTienda($recompensa);
        $precio = (float) ($articulo->puntos_necesarios / 100);

        // 1. Crear factura
        $factura = Factura::create([
            'user_id'         => $user->id,
            'importe'         => $precio,
            'concepto'        => "Compra de artículo: {$articulo->nombre}",
            'nombre_titular'  => $user->name,
            'email_titular'   => $user->email,
            'ultimos_digitos' => 'PAYP',
        ]);

        // 2. Dar artículo
        DB::transaction(function () use ($user, $articulo) {
            CompraTienda::create([
                'user_id' => $user->id,
                'recompensa_id' => $articulo->id,
                'puntos_gastados' => 0, // Pagado con PayPal
            ]);

            Inventario::create([
                'user_id' => $user->id,
                'recompensa_id' => $articulo->id,
                'origen' => 'tienda_paypal',
                'obtenida_at' => now(),
            ]);
        });

        $this->pointsHistoryService->log(
            $user,
            'spent',
            0,
            'Compra PayPal de articulo: ' . $articulo->nombre
        );

        $this->notificationService->notify(
            $user->id,
            'billing',
            'Articulo desbloqueado',
            'Tu compra de "' . $articulo->nombre . '" se completo correctamente.',
            route('usuario.inventario')
        );

        // 3. Generar PDF y enviar correo
        $pdf = Pdf::loadView('pdf.factura', [
            'factura' => $factura,
            'user'    => $user,
        ]);

        try {
            Mail::to($user->email)->send(new FacturaPagoMail($factura, $pdf->output()));
        } catch (\Exception $e) {
            \Log::error('Error enviando email de factura (articulo): ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Artículo comprado con éxito',
            'redirect' => route('pago.exito', ['factura' => $factura->id])
        ]);
    }
}
