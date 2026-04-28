<?php

namespace App\Http\Controllers;

use App\Models\PaseDePaseo;
use App\Models\PuntosHistorial;
use App\Models\Recompensa;
use App\Models\User;
use App\Services\PointsHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaseDePaseoController extends Controller
{
    public function __construct(private PointsHistoryService $pointsHistoryService)
    {
    }

    public function index()
    {
        $user = Auth::user() ?? User::first();

        if (!$user) {
            return redirect()->route('login');
        }

        $pase = PaseDePaseo::with('recompensas')->latest('id')->first();

        if (!$pase) {
            return view('pase_paseo', [
                'pase' => null,
                'niveles' => [],
                'nivelUsuario' => (int) $user->nivel,
                'esPremium' => (bool) $user->premium,
                'reclamadas' => [],
            ])->with('status', 'No hay pase de paseo activo en este momento.');
        }

        // Obtener recompensas ya reclamadas por el usuario
        $recompensasReclamadas = $user->recompensas()
            ->where('tipo', 'pase_de_paseo')
            ->pluck('recompensas.id')
            ->toArray();

        $recompensasReclamadasPorPuntos = PuntosHistorial::query()
            ->where('user_id', $user->id)
            ->where('tipo', 'reward')
            ->where('related_model', Recompensa::class)
            ->whereNotNull('related_model_id')
            ->pluck('related_model_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $recompensasReclamadas = array_values(array_unique(array_merge(
            $recompensasReclamadas,
            $recompensasReclamadasPorPuntos,
        )));

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

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

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

        $puntosGanados = max(0, (int) $recompensa->puntos_necesarios);
        $esRecompensaDePuntos = $puntosGanados > 0;

        $yaReclamada = $esRecompensaDePuntos
            ? PuntosHistorial::query()
                ->where('user_id', $user->id)
                ->where('tipo', 'reward')
                ->where('related_model', Recompensa::class)
                ->where('related_model_id', $recompensa->id)
                ->exists()
            : $user->recompensas()
                ->where('recompensas.id', $recompensa->id)
                ->exists();

        if ($yaReclamada) {
            return response()->json(['message' => 'Ya has reclamado esta recompensa.'], 409);
        }

        DB::transaction(function () use ($user, $recompensa, $puntosGanados, $esRecompensaDePuntos) {
            if ($esRecompensaDePuntos) {
                $user->increment('puntos', $puntosGanados);

                $this->pointsHistoryService->log(
                    $user,
                    'reward',
                    $puntosGanados,
                    'Recompensa del Pase de paseo: ' . $recompensa->nombre,
                    null,
                    Recompensa::class,
                    $recompensa->id
                );

                return;
            }

            // Recompensas no monetarias: se guardan en inventario para poder usarlas después.
            $user->recompensas()->attach($recompensa->id, [
                'origen' => 'pase_de_paseo',
                'obtenida_at' => Carbon::now(),
            ]);
        });

        $user->refresh();

        return response()->json([
            'recompensa_id' => $recompensa->id,
            'puntos_ganados' => $puntosGanados,
            'puntos_actuales' => (int) $user->puntos,
        ]);
    }
}
