<?php

namespace App\Services;

use App\Models\Mision;
use App\Models\User;
use Carbon\Carbon;

class MisionService
{
    /**
     * Asigna misiones diarias y semanales al usuario si le faltan.
     */
    public function assignDailyAndWeeklyMissions(User $user): void
    {
        $now = Carbon::now();

        // ── Misiones diarias ────────────────────────────────────────────
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
                    'completada'       => false,
                    'fecha_asignacion' => $now,
                    'fecha_limite'     => $dailyLimit,
                ]);
            }
        }

        // ── Misiones semanales ──────────────────────────────────────────
        $this->assignWeeklyMissions($user);
    }

    /**
     * Asigna misiones semanales si no tiene ninguna activa.
     */
    private function assignWeeklyMissions(User $user): void
    {
        $now         = Carbon::now();
        $weeklyLimit = $user->weekly_mission_cycle_end;

        $weeklyActiveIds = $user->misiones()
            ->where('misiones.semanal', true)
            ->where('misiones.evento_id', null)
            ->wherePivot('fecha_limite', '>', $now)
            ->pluck('misiones.id')
            ->all();

        if (count($weeklyActiveIds) === 0) {
            $weeklyCandidates = Mision::where('semanal', true)
                ->where('evento_id', null)
                ->whereNotIn('id', $weeklyActiveIds)
                ->inRandomOrder()
                ->limit(3)
                ->get();

            foreach ($weeklyCandidates as $mision) {
                $user->misiones()->attach($mision->id, [
                    'completada'       => false,
                    'fecha_asignacion' => $now,
                    'fecha_limite'     => $weeklyLimit,
                ]);
            }
        }
    }

    /**
     * Elimina las misiones activas no completadas y asigna nuevas (renovación manual).
     *
     * @param  string  $tipo  'todas' | 'diarias' | 'semanales'
     */
    public function renovarMisiones(User $user, string $tipo = 'todas'): void
    {
        $now = Carbon::now();

        if ($tipo === 'todas' || $tipo === 'diarias') {
            $ids = $user->misiones()
                ->where('misiones.semanal', false)
                ->where('misiones.evento_id', null)
                ->wherePivot('completada', false)
                ->wherePivot('fecha_limite', '>', $now)
                ->pluck('misiones.id');

            $user->misiones()->detach($ids);
        }

        if ($tipo === 'todas' || $tipo === 'semanales') {
            $ids = $user->misiones()
                ->where('misiones.semanal', true)
                ->where('misiones.evento_id', null)
                ->wherePivot('completada', false)
                ->wherePivot('fecha_limite', '>', $now)
                ->pluck('misiones.id');

            $user->misiones()->detach($ids);
            
            // Al renovar las semanales, actualizamos el ciclo para que tengan 7 días desde ahora
            $user->weekly_mission_cycle_end = Carbon::now()->addDays(7);
            $user->save();
        }

        $this->assignDailyAndWeeklyMissions($user);
    }
}
