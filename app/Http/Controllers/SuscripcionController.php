<?php

namespace App\Http\Controllers;

use App\Models\TarjetaBancaria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuscripcionController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? User::first();
        // Cargamos todas las tarjetas bancarias del usuario
        $tarjetas = $user->tarjetasBancarias;

        return view('suscripcion', [
            'tarjetas' => $tarjetas,
            'esPremium' => (bool)$user->premium,
        ]);
    }

    public function storeCard(Request $request)
    {
        $user = Auth::user() ?? User::first();

        $validated = $request->validate([
            'numero_tarjeta' => ['required', 'string', 'max:24'],
            'fecha_caducidad' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/(\d{2})$/'],
            'codigo_seguridad' => ['required', 'digits:3'],
            'titular' => ['required', 'string', 'max:120'],
        ], [
            'numero_tarjeta.required' => 'El número de tarjeta es obligatorio.',
            'fecha_caducidad.required' => 'La fecha de caducidad es obligatoria.',
            'fecha_caducidad.regex' => 'El formato de fecha debe ser MM/YY (ej: 12/26).',
            'codigo_seguridad.required' => 'El código de seguridad es obligatorio.',
            'codigo_seguridad.digits' => 'El código CVC debe tener exactamente 3 dígitos.',
            'titular.required' => 'El nombre del titular es obligatorio.',
            'titular.max' => 'El nombre del titular no puede exceder los 120 caracteres.',
        ]);

        $soloDigitos = preg_replace('/\D+/', '', $validated['numero_tarjeta']);

        if (strlen($soloDigitos) !== 16) {
            return back()
                ->withErrors(['numero_tarjeta' => 'El número de tarjeta debe tener exactamente 16 dígitos.'])
                ->withInput();
        }

        $ultimosCuatro = substr($soloDigitos, -4);
        $numeroEnmascarado = '**** **** **** ' . $ultimosCuatro;

        // Ahora usamos CREATE en lugar de updateOrCreate para permitir múltiples tarjetas
        TarjetaBancaria::create([
            'user_id' => $user->id,
            'titular' => $validated['titular'],
            'numero_enmascarado' => $numeroEnmascarado,
            'token_pago' => 'tok_simulated_' . bin2hex(random_bytes(8)),
            'marca' => 'Visa',
        ]);

        return redirect()->back()->with('success', 'Tarjeta añadida correctamente.');
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user() ?? User::first();

        if ($user->premium) {
            return redirect()->back()->with('error', 'Ya eres un usuario Premium.');
        }

        // Validar que se haya seleccionado una tarjeta y que pertenezca al usuario
        $request->validate([
            'tarjeta_id' => 'required|exists:tarjetas_bancarias,id',
        ], [
            'tarjeta_id.required' => 'Debes seleccionar una tarjeta para suscribirte.',
        ]);

        $tarjeta = TarjetaBancaria::where('id', $request->tarjeta_id)
                                  ->where('user_id', $user->id)
                                  ->first();

        if (!$tarjeta) {
            return redirect()->back()->with('error', 'La tarjeta seleccionada no es válida.');
        }

        $user->update(['premium' => true]);

        return redirect()->route('pase.paseo')->with('success', '¡Enhorabuena! Ahora eres un usuario Premium.');
    }
}
