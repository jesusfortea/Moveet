<?php

namespace App\Http\Controllers;

use App\Models\TarjetaBancaria;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $user = Auth::user() ?? User::with(['tarjetaBancaria', 'inventario.recompensa'])->first();

        if (!$user) {
            abort(404, 'No hay usuarios disponibles.');
        }

        $user->loadMissing(['tarjetaBancaria', 'inventario.recompensa']);

        $inventario = $user->inventario
            ->sortByDesc('obtenida_at')
            ->values();

        return view('usuario.index', [
            'usuario' => $user,
            'tarjeta' => $user->tarjetaBancaria,
            'inventario' => $inventario,
        ]);
    }

    public function createCard(): View
    {
        $user = Auth::user() ?? User::first();

        if (!$user) {
            abort(404, 'No hay usuarios disponibles.');
        }

        return view('usuario.tarjeta', [
            'usuario' => $user,
        ]);
    }

    public function storeCard(Request $request): RedirectResponse
    {
        $user = Auth::user() ?? User::first();

        if (!$user) {
            abort(404, 'No hay usuarios disponibles.');
        }

        $validated = $request->validate([
            'numero_tarjeta' => ['required', 'string', 'max:24'],
            'fecha_caducidad' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/(\d{2})$/'],
            'codigo_seguridad' => ['required', 'digits_between:3,4'],
            'titular' => ['required', 'string', 'max:120'],
        ]);

        $soloDigitos = preg_replace('/\D+/', '', $validated['numero_tarjeta']);

        if (strlen($soloDigitos) < 12 || strlen($soloDigitos) > 19) {
            return back()
                ->withErrors(['numero_tarjeta' => 'El numero de tarjeta no es valido.'])
                ->withInput();
        }

        $ultimosCuatro = substr($soloDigitos, -4);
        $numeroEnmascarado = '**** **** **** ' . $ultimosCuatro;

        TarjetaBancaria::updateOrCreate(
            ['user_id' => $user->id],
            [
                'titular' => $validated['titular'],
                'numero_enmascarado' => $numeroEnmascarado,
            ]
        );

        return redirect()
            ->route('usuario.index')
            ->with('status', 'Tarjeta guardada correctamente.');
    }
}
