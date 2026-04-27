<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $renombres = [
        // ─── DIARIAS ────────────────────────────────────────────────
        'Primer paso del día'   => 'Camina 100 metros',
        'Paseo matutino'        => 'Camina 250 metros',
        'Caminar 500 metros'    => 'Camina 500 metros',
        'Media hora andando'    => 'Camina 750 metros',
        'El kilómetro diario'   => 'Camina 1 kilómetro',
        'Descubridor de calles' => 'Camina 1,2 kilómetros',
        'Explorador urbano'     => 'Camina 1,5 kilómetros',
        'Ruta al parque'        => 'Camina 2 kilómetros',
        'Caminante dedicado'    => 'Camina 3 kilómetros',
        'Ruta exprés'           => 'Camina 4 kilómetros',

        // ─── SEMANALES ──────────────────────────────────────────────
        'Semana activa'           => 'Camina 3 km esta semana',
        'Media maratón urbana'    => 'Camina 5 km esta semana',
        'Conquistador de barrios' => 'Camina 6 km esta semana',
        'Gran explorador'         => 'Camina 8 km esta semana',
        'Reto de los 10K'         => 'Camina 10 km esta semana',
        'Velocista de la semana'  => 'Camina 15 km esta semana',
        'Maratonista urbano'      => 'Camina 20 km esta semana',
        'Paseo de Barcelona'      => 'Visita el Passeig de Gràcia',
        'La Barceloneta'          => 'Visita la playa de la Barceloneta',
        'Parc de la Ciutadella'   => 'Visita el Parc de la Ciutadella',
    ];

    public function up(): void
    {
        foreach ($this->renombres as $viejo => $nuevo) {
            DB::table('misiones')
                ->where('nombre', $viejo)
                ->where('evento_id', null)
                ->update(['nombre' => $nuevo]);
        }
    }

    public function down(): void
    {
        foreach ($this->renombres as $viejo => $nuevo) {
            DB::table('misiones')
                ->where('nombre', $nuevo)
                ->where('evento_id', null)
                ->update(['nombre' => $viejo]);
        }
    }
};
