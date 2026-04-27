<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\User;
use App\Mail\FacturaPagoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SuscripcionController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? User::first();

        return view('suscripcion', [
            'esPremium' => (bool)$user->premium,
            'premiumUntil' => $user->premium_until,
        ]);
    }

    public function capturarPayPalPremium(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No autorizado'], 401);
        }

        // 1. Crear factura
        $factura = Factura::create([
            'user_id'         => $user->id,
            'importe'         => 19.99,
            'concepto'        => 'Suscripción Premium Moveet',
            'nombre_titular'  => $user->name,
            'email_titular'   => $user->email,
            'ultimos_digitos' => 'PAYP',
        ]);

        // 2. Activamos Premium por 1 mes
        $user->update([
            'premium' => true,
            'premium_until' => now()->addMonth(),
        ]);

        // 3. Generar PDF y enviar correo
        $pdf = Pdf::loadView('pdf.factura', [
            'factura' => $factura,
            'user'    => $user,
        ]);

        try {
            Mail::to($user->email)->send(new FacturaPagoMail($factura, $pdf->output()));
        } catch (\Exception $e) {
            \Log::error('Error enviando email de factura (premium): ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => '¡Ahora eres Premium!',
            'redirect' => route('pago.exito', ['factura' => $factura->id])
        ]);
    }
}
