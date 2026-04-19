<?php

namespace Database\Seeders;

use App\Models\PackPuntos;
use Illuminate\Database\Seeder;

class PackPuntosSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            [
                'nombre' => 'Pack basico',
                'puntos' => 400,
                'precio_euros' => 4.99,
                'destacado' => false,
                'activo' => true,
                'orden' => 1,
                'ruta_imagen' => 'img/Moneda.png',
            ],
            [
                'nombre' => 'Pack destacado',
                'puntos' => 1700,
                'precio_euros' => 17.99,
                'destacado' => true,
                'activo' => true,
                'orden' => 2,
                'ruta_imagen' => 'img/Moneda.png',
            ],
            [
                'nombre' => 'Pack premium',
                'puntos' => 3000,
                'precio_euros' => 34.99,
                'destacado' => false,
                'activo' => true,
                'orden' => 3,
                'ruta_imagen' => 'img/Moneda.png',
            ],
        ];

        foreach ($packs as $pack) {
            PackPuntos::firstOrCreate(
                [
                    'nombre' => $pack['nombre'],
                ],
                $pack
            );
        }
    }
}
