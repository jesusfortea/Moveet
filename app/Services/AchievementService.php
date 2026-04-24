<?php

namespace App\Services;

use App\Models\Logro;
use App\Models\User;
use Carbon\Carbon;

class AchievementService
{
    public function syncBaseAchievements(User $user): void
    {
        $definitions = [
            [
                'slug' => 'racha_7_dias',
                'nombre' => 'Constancia de Acero',
                'descripcion' => 'Mantén una racha de 7 días.',
                'icono' => 'streak',
                'puntos_bonus' => 150,
                'condition' => fn (User $u) => (int) $u->current_streak >= 7,
            ],
            [
                'slug' => 'misiones_50',
                'nombre' => 'Maratoniano',
                'descripcion' => 'Completa 50 misiones.',
                'icono' => 'runner',
                'puntos_bonus' => 300,
                'condition' => function (User $u): bool {
                    $completed = $u->misiones()->wherePivot('completada', true)->count();
                    return $completed >= 50;
                },
            ],
            [
                'slug' => 'socializador_10',
                'nombre' => 'Socializador',
                'descripcion' => 'Consigue 10 contactos aceptados.',
                'icono' => 'social',
                'puntos_bonus' => 200,
                'condition' => function (User $u): bool {
                    return $u->contactos()->count() >= 10;
                },
            ],
            [
                'slug' => 'nocturno_100km',
                'nombre' => 'Caminante Nocturno',
                'descripcion' => 'Registra 100 km en horario nocturno.',
                'icono' => 'night',
                'puntos_bonus' => 400,
                'condition' => function (User $u): bool {
                    $nightDistance = $u->rutasCompletadas()
                        ->join('rutas_usuario', 'ruta_usuario_completions.ruta_usuario_id', '=', 'rutas_usuario.id')
                        ->whereRaw('HOUR(completed_at) >= 22 OR HOUR(completed_at) < 6')
                        ->sum('rutas_usuario.distancia_metros');

                    return $nightDistance >= 100000;
                },
            ],
        ];

        foreach ($definitions as $definition) {
            $logro = Logro::firstOrCreate(
                ['slug' => $definition['slug']],
                [
                    'nombre' => $definition['nombre'],
                    'descripcion' => $definition['descripcion'],
                    'icono' => $definition['icono'],
                    'puntos_bonus' => $definition['puntos_bonus'],
                    'activo' => true,
                ]
            );

            $alreadyUnlocked = $user->logros()->where('logro_id', $logro->id)->exists();

            if ($alreadyUnlocked || !$definition['condition']($user)) {
                continue;
            }

            $user->logros()->attach($logro->id, ['achieved_at' => Carbon::now()]);
            $user->increment('puntos', (int) $logro->puntos_bonus);
        }
    }
}
