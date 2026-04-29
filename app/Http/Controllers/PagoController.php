<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\User;
use App\Mail\FacturaPagoMail;
use App\Services\MisionService;
use App\Services\PointsHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    public function __construct(
        private MisionService $misionService,
        private PointsHistoryService $pointsHistoryService,
    ) {}

    public function mostrarPasarela(Request $request)
    {
        $producto = [
            'nombre' => 'Cambiar misiones',
            'precio' => 0.99,
        ];

        return view('pago.pasarela', compact('producto'));
    }

    public function capturarPayPalMisiones(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No autorizado'], 401);
        }

        // 1. Crear factura (Simulando datos de tarjeta desde PayPal si fuera necesario, 
        // pero aquí usamos datos genéricos de PayPal)
        $factura = Factura::create([
            'user_id'         => $user->id,
            'importe'         => 0.99,
            'concepto'        => 'Renovación de misiones (PayPal)',
            'nombre_titular'  => $user->name,
            'email_titular'   => $user->email,
            'ultimos_digitos' => 'PAYP', // Identificador de método
        ]);

        // 2. Renovar misiones usando MisionService (DI correcta)
        $this->misionService->renovarMisiones($user, 'todas');

        $this->pointsHistoryService->log(
            $user,
            'spent',
            0,
            'Pago PayPal: Renovacion de misiones',
            null,
            Factura::class,
            $factura->id
        );

        // 3. Generar PDF
        $pdf = Pdf::loadView('pdf.factura', [
            'factura' => $factura,
            'user'    => $user,
        ]);

        // 4. Enviar correo con la factura adjunta
        try {
            Mail::to($user->email)->send(new FacturaPagoMail($factura, $pdf->output()));
        } catch (\Exception $e) {
            \Log::error('Error enviando email de factura: ' . $e->getMessage());
        }

        return response()->json([
            'status'   => 'success',
            'message'  => 'Pago realizado con éxito',
            'redirect' => route('pago.exito', ['factura' => $factura->id])
        ]);
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
