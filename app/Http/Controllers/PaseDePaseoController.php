<?php

namespace App\Http\Controllers;

use App\Models\PaseDePaseo;
use App\Models\Recompensa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaseDePaseoController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? User::first();
        
        $pase = PaseDePaseo::with('recompensas')->first();
        
        if (!$pase) {
            return redirect()->route('home')->with('error', 'No hay pase de paseo activo en este momento.');
        }

        // Obtener recompensas ya reclamadas por el usuario
        $recompensasReclamadas = $user->recompensas()
            ->where('tipo', 'pase_de_paseo')
            ->pluck('recompensas.id')
            ->toArray();

        // Agrupar recompensas por nivel
        $niveles = [];
        foreach ($pase->recompensas as $recompensa) {
            $lvl = $recompensa->nivel_necesario;
            if (!isset($niveles[$lvl])) {
                $niveles[$lvl] = [
                    'gratis' => null,
                    'premium' => null,
                ];
            }
            
            if ($recompensa->premium) {
                $niveles[$lvl]['premium'] = $recompensa;
            } else {
                $niveles[$lvl]['gratis'] = $recompensa;
            }
        }
        
        ksort($niveles);

        // Usamos el nivel global del usuario para determinar su progreso en el pase
        $nivelUsuario = $user->nivel;

        return view('pase_paseo', [
            'pase' => $pase,
            'niveles' => $niveles,
            'nivelUsuario' => $nivelUsuario,
            'esPremium' => (bool)$user->premium,
            'reclamadas' => $recompensasReclamadas,
        ]);
    }

    public function reclamar(Request $request, Recompensa $recompensa)
    {
        $user = Auth::user() ?? User::first();

        // 1. Validar que la recompensa sea de tipo pase_de_paseo
        if ($recompensa->tipo !== 'pase_de_paseo') {
            return response()->json(['message' => 'Esta recompensa no pertenece al pase de paseo.'], 403);
        }

        // 2. Validar nivel
        if ($user->nivel < $recompensa->nivel_necesario) {
            return response()->json(['message' => 'Nivel insuficiente para reclamar esta recompensa.'], 403);
        }

        // 3. Validar premium
        if ($recompensa->premium && !$user->premium) {
            return response()->json(['message' => 'Esta recompensa requiere suscripción premium.'], 403);
        }

        // 4. Validar que no se haya reclamado ya
        $yaReclamada = $user->recompensas()
            ->where('recompensas.id', $recompensa->id)
            ->exists();

        if ($yaReclamada) {
            return response()->json(['message' => 'Ya has reclamado esta recompensa.'], 409);
        }

        // 5. Añadir al inventario
        $user->recompensas()->attach($recompensa->id, [
            'origen' => 'pase_de_paseo',
            'obtenida_at' => Carbon::now(),
        ]);

        return response()->json([
            // Devolvemos solo el ID de la recompensa para no lanzar alertas innecesarias en la UI
            'recompensa_id' => $recompensa->id
        ]);
    }
}
