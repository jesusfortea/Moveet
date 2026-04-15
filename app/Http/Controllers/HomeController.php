<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Mision;
use App\Models\User;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Obtener usuario autenticado, o el primero para pruebas
        $user = Auth::user() ?? User::first();

        if (!$user) {
            return view('home', [
                'misiones' => [],
                'fechaLimiteDiarias' => Carbon::now()->addDay()->toISOString(),
                'fechaLimiteSemanales' => Carbon::now()->addDays(7)->toISOString(),
            ]);
        }

        $this->assignDailyAndWeeklyMissions($user);

        // Obtener misiones activas para el usuario, incluidas las completadas
        $misiones = $user->misiones()
            ->wherePivot('fecha_limite', '>', Carbon::now())
            ->with('evento') // si necesitas datos del evento
            ->get()
            ->map(function ($userMision) {
                $pivot = $userMision->pivot;
                return [
                    'id' => $userMision->id,
                    'nombre' => $userMision->nombre,
                    'puntos' => $userMision->puntos,
                    'semanal' => $userMision->semanal,
                    'completada' => $pivot->completada,
                    'premium' => $userMision->premium,
                    'ejeX' => $userMision->ejeX,
                    'ejeY' => $userMision->ejeY,
                    'direccion' => $userMision->direccion,
                    'metros_requeridos' => $userMision->metros_requeridos,
                    'fecha_limite' => $pivot->fecha_limite,
                ];
            });

        $activeDailyLimit = $user->misiones()
            ->where('misiones.semanal', false)
            ->wherePivot('fecha_limite', '>', Carbon::now())
            ->min('user_mision.fecha_limite');

        $activeWeeklyLimit = $user->misiones()
            ->where('misiones.semanal', true)
            ->wherePivot('fecha_limite', '>', Carbon::now())
            ->min('user_mision.fecha_limite');

        return view('home', [
            'misiones' => $misiones,
            'fechaLimiteDiarias' => $activeDailyLimit ? Carbon::parse($activeDailyLimit)->toISOString() : Carbon::now()->addDay()->toISOString(),
            'fechaLimiteSemanales' => $activeWeeklyLimit ? Carbon::parse($activeWeeklyLimit)->toISOString() : Carbon::now()->addDays(7)->toISOString(),
        ]);
    }

    public function completarMision(Request $request, Mision $mision)
    {
        $user = Auth::user() ?? User::first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 401);
        }

        $usuarioMision = $user->misiones()
            ->where('misiones.id', $mision->id)
            ->first();

        if (!$usuarioMision) {
            return response()->json(['message' => 'Misión no asignada'], 404);
        }

        if ($usuarioMision->pivot->completada) {
            return response()->json([
                'message' => 'Misión ya completada',
                'puntos' => $user->puntos,
            ]);
        }

        DB::transaction(function () use ($user, $mision) {
            $user->increment('puntos', $mision->puntos);
            $user->misiones()->updateExistingPivot($mision->id, [
                'completada' => true,
                'fecha_completado' => Carbon::now(),
            ]);
        });

        return response()->json([
            'message' => 'Misión completada',
            'mision_id' => $mision->id,
            'puntos' => $user->fresh()->puntos,
        ]);
    }

    private function assignDailyAndWeeklyMissions(User $user): void
    {
        $now = Carbon::now();

        $dailyActiveIds = $user->misiones()
            ->where('misiones.semanal', false)
            ->wherePivot('fecha_limite', '>', $now)
            ->pluck('misiones.id')
            ->all();

        $weeklyActiveIds = $user->misiones()
            ->where('misiones.semanal', true)
            ->wherePivot('fecha_limite', '>', $now)
            ->pluck('misiones.id')
            ->all();

        $dailyNeeded = max(0, 3 - count($dailyActiveIds));
        if ($dailyNeeded > 0) {
            $dailyCandidates = Mision::where('semanal', false)
                ->whereNotIn('id', $dailyActiveIds)
                ->inRandomOrder()
                ->limit($dailyNeeded)
                ->get();

            foreach ($dailyCandidates as $mision) {
                $user->misiones()->attach($mision->id, [
                    'completada' => false,
                    'fecha_asignacion' => $now,
                    'fecha_limite' => $now->copy()->addDay(),
                ]);
            }
        }

        $weeklyNeeded = max(0, 3 - count($weeklyActiveIds));
        if ($weeklyNeeded > 0) {
            $weeklyCandidates = Mision::where('semanal', true)
                ->whereNotIn('id', $weeklyActiveIds)
                ->inRandomOrder()
                ->limit($weeklyNeeded)
                ->get();

            foreach ($weeklyCandidates as $mision) {
                $user->misiones()->attach($mision->id, [
                    'completada' => false,
                    'fecha_asignacion' => $now,
                    'fecha_limite' => $now->copy()->addDays(7),
                ]);
            }
        }
    }
}
