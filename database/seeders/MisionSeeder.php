<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evento;
use App\Models\Mision;
use App\Models\User;
use Carbon\Carbon;

class MisionSeeder extends Seeder
{
    public function run(): void
    {
        $evento = Evento::where('nombre', 'Festival Moveet')->first();

        // Crear misiones de ejemplo
        $misiones = [
            [
                'nombre' => 'Caminar 500 metros.',
                'descripcion' => 'Camina al menos 500 metros para completar esta misión.',
                'metros_requeridos' => 500,
                'ejeX' => null,
                'ejeY' => null,
                'direccion' => null,
                'premium' => false,
                'semanal' => false,
                'puntos' => 30,
                'evento_id' => null,
            ],
            [
                'nombre' => 'Visitar Londres',
                'descripcion' => 'Visitar Londres.',
                'metros_requeridos' => null,
                'ejeX' => null,
                'ejeY' => null,
                'direccion' => null,
                'premium' => false,
                'semanal' => false,
                'puntos' => 30,
                'evento_id' => null,
            ],
            [
                'nombre' => 'Ruta del Festival',
                'descripcion' => 'Completa la ruta especial dentro del Festival Moveet.',
                'metros_requeridos' => 1000,
                'ejeX' => 40.4180,
                'ejeY' => -3.7050,
                'direccion' => 'Parque del Retiro, Madrid',
                'premium' => false,
                'semanal' => false,
                'puntos' => 50,
                'evento_id' => $evento ? $evento->id : null,
            ],
            [
                'nombre' => 'Foto en el stand principal',
                'descripcion' => 'Haz una foto en el stand principal del Festival Moveet.',
                'metros_requeridos' => null,
                'ejeX' => 40.4172,
                'ejeY' => -3.7080,
                'direccion' => 'Stand principal, Plaza Mayor',
                'premium' => true,
                'semanal' => false,
                'puntos' => 80,
                'evento_id' => $evento ? $evento->id : null,
            ],
            [
                'nombre' => 'Reúne 3 medallas del evento',
                'descripcion' => 'Consigue tres medallas especiales del Festival Moveet.',
                'metros_requeridos' => null,
                'ejeX' => null,
                'ejeY' => null,
                'direccion' => null,
                'premium' => false,
                'semanal' => true,
                'puntos' => 120,
                'evento_id' => $evento ? $evento->id : null,
            ],
        ];

        foreach ($misiones as $data) {
            Mision::create($data);
        }

        // Asignar misiones a todos los usuarios
        $users = User::all();
        $misionIds = Mision::pluck('id');

        foreach ($users as $user) {
            foreach ($misionIds as $misionId) {
                $mision = Mision::find($misionId);
                $user->misiones()->attach($misionId, [
                    'completada' => false,
                    'fecha_asignacion' => Carbon::now(),
                    'fecha_limite' => Carbon::now()->addDays($mision->semanal ? 7 : 1),
                ]);
            }
        }
    }
}