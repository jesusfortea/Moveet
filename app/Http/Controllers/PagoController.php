<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\User;
use App\Mail\FacturaPagoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PagoController extends Controller
{
    public function mostrarPasarela(Request $request)
    {
        // En un futuro podríamos recibir el tipo de producto. Por ahora asumimos "Cambiar misiones" a 0.99
        $producto = [
            'nombre' => 'Cambiar misiones',
            'precio' => 0.99,
        ];

        $tarjetas = Auth::user()->tarjetasBancarias ?? collect();

        return view('pago.pasarela', compact('producto', 'tarjetas'));
    }

    public function procesarPago(Request $request)
    {
        $request->validate([
            'tarjeta_id' => 'required|exists:tarjetas_bancarias,id',
        ], [
            'tarjeta_id.required' => 'Debes seleccionar una tarjeta para realizar el pago.',
        ]);

        $user = Auth::user();
        
        $tarjeta = \App\Models\TarjetaBancaria::where('id', $request->tarjeta_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$tarjeta) {
            return back()->withErrors(['tarjeta_id' => 'La tarjeta seleccionada no es válida.'])->withInput();
        }

        // Extraer últimos 4 dígitos
        $ultimosDigitos = substr($tarjeta->numero_enmascarado, -4);

        // SIMULACIÓN: Procesamos el cobro de 0.99€ exitosamente (Sandbox)
        
        // 1. Crear factura
        $factura = Factura::create([
            'user_id'         => $user->id,
            'importe'         => 0.99,
            'concepto'        => 'Renovación de misiones (Sandbox)',
            'nombre_titular'  => $tarjeta->titular,
            'email_titular'   => $user->email,
            'ultimos_digitos' => $ultimosDigitos,
        ]);

        // 2. Renovar misiones llamando al HomeController
        $homeController = new HomeController();
        $homeController->renovarMisiones($user, 'todas');

        // 3. Generar PDF
        $pdf = Pdf::loadView('pdf.factura', [
            'factura' => $factura,
            'user'    => $user,
        ]);

        // 4. Enviar correo con la factura adjunta
        try {
            Mail::to($user->email)->send(new FacturaPagoMail($factura, $pdf->output()));
        } catch (\Exception $e) {
            // Logear el error de correo, pero no bloquear el proceso de éxito para el usuario
            \Log::error('Error enviando email de factura: ' . $e->getMessage());
        }

        // 5. Redirigir a éxito
        return redirect()->route('pago.exito', ['factura' => $factura->id]);
    }

    public function exito(Factura $factura)
    {
        // Verificar que la factura pertenece al usuario actual
        if ($factura->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pago.exito', compact('factura'));
    }

    public function descargarFactura(Factura $factura)
    {
        if ($factura->user_id !== Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.factura', [
            'factura' => $factura,
            'user'    => $factura->user,
        ]);

        return $pdf->download('factura_' . str_pad($factura->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}
