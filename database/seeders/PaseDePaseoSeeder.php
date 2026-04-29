<?php

namespace Database\Seeders;

use App\Models\PaseDePaseo;
use App\Models\Recompensa;
use Illuminate\Database\Seeder;

class PaseDePaseoSeeder extends Seeder
{
    public function run(): void
    {
        $pase = PaseDePaseo::create([
            'nombre' => 'Pase de Paseo Primavera 2026',
            'ruta_imagen' => 'img/pases/primavera_2026.png',
        ]);

        $recompensas = [
            // Nivel 1
            [
                'pase_de_paseo_id' => $pase->id,
                'nombre' => 'Potenciador de Puntos',
                'descripcion' => 'Duplica los puntos obtenidos durante 30 minutos.',
                'premium' => true,
                'puntos_necesarios' => 0,
                'nivel_necesario' => 1,
                'ruta_imagen' => 'img/recompensas/item_dice.png',
                'tipo' => 'pase_de_paseo',
            ],
            // Nivel 2
            [
                'pase_de_paseo_id' => $pase->id,
                'nombre' => 'Fondo Premium',
                'descripcion' => 'Un fondo exclusivo para tu perfil.',
                'premium' => true,
                'puntos_necesarios' => 0,
                'nivel_necesario' => 2,
                'ruta_imagen' => 'img/recompensas/item_bg.png',
                'tipo' => 'pase_de_paseo',
            ],
            // Nivel 3
            [
                'pase_de_paseo_id' => $pase->id,
                'nombre' => 'Potenciador de EXP',
                'descripcion' => 'Aumenta la experiencia ganada en misiones.',
                'premium' => false,
                'puntos_necesarios' => 0,
                'nivel_necesario' => 3,
                'ruta_imagen' => 'img/recompensas/item_potion.png',
                'tipo' => 'pase_de_paseo',
            ],
            [
                'pase_de_paseo_id' => $pase->id,
                'nombre' => '1 Cambiazo gratis',
                'descripcion' => 'Cambia una misión diaria sin coste.',
                'premium' => true,
                'puntos_necesarios' => 0,
                'nivel_necesario' => 3,
                'ruta_imagen' => 'img/recompensas/item_refresh.png',
                'tipo' => 'pase_de_paseo',
            ],
            // Nivel 4
            [
                'pase_de_paseo_id' => $pase->id,
                'nombre' => '50 Puntos Extra',
                'descripcion' => 'Recibe 50 puntos para gastar en la tienda.',
                'premium' => false,
                'puntos_necesarios' => 50,
                'nivel_necesario' => 4,
                'ruta_imagen' => 'img/recompensas/item_points.png',
                'tipo' => 'pase_de_paseo',
            ],
            // Nivel 5
            [
                'pase_de_paseo_id' => $pase->id,
                'nombre' => 'Avatar Exclusivo',
                'descripcion' => 'Un marco dorado para tu avatar.',
                'premium' => true,
                'puntos_necesarios' => 0,
                'nivel_necesario' => 5,
                'ruta_imagen' => 'img/recompensas/item_avatar.png',
                'tipo' => 'pase_de_paseo',
            ],
        ];

        foreach ($recompensas as $recompensa) {
            Recompensa::create($recompensa);
        }
    }
}
