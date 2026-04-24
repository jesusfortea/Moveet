<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Mision;
use App\Models\User;
use App\Services\AchievementService;
use App\Services\AntiCheatService;
use App\Services\PointsHistoryService;
use App\Services\ReferralService;
use App\Services\StreakService;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct(
        private StreakService $streakService,
        private AntiCheatService $antiCheatService,
        private PointsHistoryService $pointsHistoryService,
        private AchievementService $achievementService,
        private ReferralService $referralService,
    )
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $this->streakService->syncStreakState($user);

        // Gestionar ciclo semanal basado en fecha de registro
        if (!$user->weekly_mission_cycle_end) {
            $initialCycleEnd = $user->created_at->copy()->addDays(7);
            if (Carbon::now()->greaterThan($initialCycleEnd)) {
                $user->weekly_mission_cycle_end = Carbon::now()->addDays(7);
            } else {
                $user->weekly_mission_cycle_end = $initialCycleEnd;
            }
            $user->save();
        }

        // Si el ciclo ha terminado, resetear a la siguiente semana
        while (Carbon::now()->greaterThan($user->weekly_mission_cycle_end)) {
            $user->weekly_mission_cycle_end = $user->weekly_mission_cycle_end->copy()->addDays(7);
        }
        $user->save();

        $this->assignDailyAndWeeklyMissions($user);

        $weeklyReset = $user->weekly_mission_cycle_end;

        // Obtener misiones activas para el usuario, incluidas las completadas (solo normales, no de evento)
        $misiones = $user->misiones()
            ->wherePivot('fecha_limite', '>', Carbon::now())
            ->where('evento_id', null) // Solo misiones normales
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
            ->where('misiones.evento_id', null) // Solo normales
            ->wherePivot('fecha_limite', '>', Carbon::now())
            ->min('user_mision.fecha_limite');

        return view('home', [
            'misiones' => $misiones,
            'fechaLimiteDiarias' => $activeDailyLimit ? Carbon::parse($activeDailyLimit)->toISOString() : Carbon::now()->addDay()->toISOString(),
            'fechaLimiteSemanales' => $weeklyReset->toISOString(),
        ]);
    }

    public function completarMision(Request $request, Mision $mision)
    {
        $user = Auth::user() ?? User::first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 401);
        }

        $validated = $request->validate([
            'distance_meters' => ['nullable', 'numeric', 'min:0'],
            'speed_kmh' => ['nullable', 'numeric', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0'],
        ]);

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

        $distanceMeters = (float) ($validated['distance_meters'] ?? 0);
        $speedKmh = (float) ($validated['speed_kmh'] ?? 0);
        $hasGeoData = isset($validated['latitude']) && isset($validated['longitude']);

        if ((int) $mision->metros_requeridos > 0 && $distanceMeters < (int) $mision->metros_requeridos) {
            return response()->json([
                'message' => 'No has completado la distancia minima requerida para esta mision.',
            ], 422);
        }

        if ($hasGeoData) {
            $antiCheat = $this->antiCheatService->validateLocationAndSpeed(
                (float) $validated['latitude'],
                (float) $validated['longitude'],
                $speedKmh,
                isset($validated['accuracy_meters']) ? (float) $validated['accuracy_meters'] : null,
                $user
            );

            if (!$antiCheat['valid']) {
                $this->antiCheatService->logSuspiciousActivity(
                    $user,
                    implode(' | ', $antiCheat['alerts']),
                    isset($validated['latitude']) ? (float) $validated['latitude'] : null,
                    isset($validated['longitude']) ? (float) $validated['longitude'] : null,
                    $speedKmh
                );

                return response()->json([
                    'message' => 'Actividad no valida detectada. Mision rechazada por seguridad.',
                    'alerts' => $antiCheat['alerts'],
                ], 422);
            }
        }

        DB::transaction(function () use ($user, $mision) {
            $user->increment('puntos', $mision->puntos);
            $user->misiones()->updateExistingPivot($mision->id, [
                'completada' => true,
                'fecha_completado' => Carbon::now(),
            ]);
        });

        if ($hasGeoData) {
            $user->update([
                'last_location_latitude' => $validated['latitude'],
                'last_location_longitude' => $validated['longitude'],
                'last_location_timestamp' => now(),
            ]);
        }

        $this->streakService->registerWalkActivity($user->fresh());

        $this->pointsHistoryService->log(
            $user,
            'mission',
            (int) $mision->puntos,
            'Mision completada: ' . $mision->nombre,
            null,
            $speedKmh > 0 ? $speedKmh : null,
            $distanceMeters > 0 ? $distanceMeters : null,
        );

        $this->referralService->processFirstMissionReward($user->fresh());
        $this->achievementService->syncBaseAchievements($user->fresh());

        $user = $user->fresh();

        return response()->json([
            'message' => 'Misión completada',
            'mision_id' => $mision->id,
            'puntos' => $user->puntos,
            'streak' => $user->current_streak,
        ]);
    }

    private function assignWeeklyMissions(User $user): void
    {
        $now = Carbon::now();
        $weeklyLimit = $user->weekly_mission_cycle_end;

        $weeklyActiveIds = $user->misiones()
            ->where('misiones.semanal', true)
            ->where('misiones.evento_id', null)
            ->wherePivot('fecha_limite', '>', $now)
            ->pluck('misiones.id')
            ->all();

        if (count($weeklyActiveIds) == 0) {
            $weeklyCandidates = Mision::where('semanal', true)
                ->where('evento_id', null)
                ->whereNotIn('id', $weeklyActiveIds)
                ->inRandomOrder()
                ->limit(3)
                ->get();

            foreach ($weeklyCandidates as $mision) {
                $user->misiones()->attach($mision->id, [
                    'completada' => false,
                    'fecha_asignacion' => $now,
                    'fecha_limite' => $weeklyLimit,
                ]);
            }
        }
    }

    private function assignDailyAndWeeklyMissions(User $user): void
    {
        $now = Carbon::now();

        // Asignar misiones diarias si no hay activas
        $dailyActiveIds = $user->misiones()
            ->where('misiones.semanal', false)
            ->where('misiones.evento_id', null)
            ->wherePivot('fecha_limite', '>', $now)
            ->pluck('misiones.id')
            ->all();

        if (count($dailyActiveIds) < 3) {
            $dailyCandidates = Mision::where('semanal', false)
                ->where('evento_id', null)
                ->whereNotIn('id', $dailyActiveIds)
                ->inRandomOrder()
                ->limit(3 - count($dailyActiveIds))
                ->get();

            $dailyLimit = Carbon::now()->addDay();

            foreach ($dailyCandidates as $mision) {
                $user->misiones()->attach($mision->id, [
                    'completada' => false,
                    'fecha_asignacion' => $now,
                    'fecha_limite' => $dailyLimit,
                ]);
            }
        }

        // Asignar misiones semanales si es necesario
        $this->assignWeeklyMissions($user);
    }

    /**
     * Elimina las misiones activas no completadas y asigna nuevas.
     */
    public function renovarMisiones(User $user, $tipo = 'todas'): void
    {
        $now = Carbon::now();

        if ($tipo === 'todas' || $tipo === 'diarias') {
            $uncompletedDaily = $user->misiones()
                ->where('misiones.semanal', false)
                ->where('misiones.evento_id', null)
                ->wherePivot('completada', false)
                ->wherePivot('fecha_limite', '>', $now)
                ->pluck('misiones.id');

            $user->misiones()->detach($uncompletedDaily);
        }

        if ($tipo === 'todas' || $tipo === 'semanales') {
            $uncompletedWeekly = $user->misiones()
                ->where('misiones.semanal', true)
                ->where('misiones.evento_id', null)
                ->wherePivot('completada', false)
                ->wherePivot('fecha_limite', '>', $now)
                ->pluck('misiones.id');

            $user->misiones()->detach($uncompletedWeekly);
        }

        // Re-asignar para cubrir los huecos dejados
        $this->assignDailyAndWeeklyMissions($user);
    }
}
