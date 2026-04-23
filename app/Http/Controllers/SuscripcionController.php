<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuscripcionController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? User::first();

        return view('suscripcion', [
            'esPremium' => (bool)$user->premium,
        ]);
    }

    public function capturarPayPalPremium(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No autorizado'], 401);
        }

        // Activamos Premium
        $user->update(['premium' => true]);

        return response()->json([
            'status' => 'success',
            'message' => '¡Ahora eres Premium!',
            'redirect' => route('pase.paseo')
        ]);
    }
}
