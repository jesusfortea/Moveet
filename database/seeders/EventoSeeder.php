<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Evento;
use Carbon\Carbon;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        Evento::updateOrCreate(
            ['nombre' => 'Festival Moveet'],
            [
                'descripcion' => 'Disfruta de un evento especial con misiones exclusivas y premios únicos.',
                'ejeX' => 40.4168,
                'ejeY' => -3.7038,
                'direccion' => 'Plaza Mayor, Madrid',
                'fecha_inicio' => Carbon::now()->subDays(2)->toDateString(),
                'fecha_fin' => Carbon::now()->addDays(4)->toDateString(),
            ]
        );

        Evento::updateOrCreate(
            ['nombre' => 'Desafío Urbano'],
            [
                'descripcion' => 'Un reto diferente para completar misiones urbanas antes de que termine el fin de semana.',
                'ejeX' => 41.3851,
                'ejeY' => 2.1734,
                'direccion' => 'Barcelona Centro',
                'fecha_inicio' => Carbon::now()->addDays(7)->toDateString(),
                'fecha_fin' => Carbon::now()->addDays(12)->toDateString(),
            ]
        );
    }
}
