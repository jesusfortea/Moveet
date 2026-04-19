<?php

namespace Database\Seeders;

use App\Models\Recompensa;
use Illuminate\Database\Seeder;

class TiendaSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Entrada gratis a PortAventura',
                'descripcion' => 'Canjea tus puntos por una entrada de un dia para PortAventura.',
                'premium' => true,
                'puntos_necesarios' => 500,
                'nivel_necesario' => 1,
                'ruta_imagen' => 'img/Moneda.png',
                'tipo' => 'tienda',
            ],
            [
                'nombre' => 'Potenciador de energia',
                'descripcion' => 'Duplica la ganancia de energia durante 24 horas.',
                'premium' => false,
                'puntos_necesarios' => 500,
                'nivel_necesario' => 1,
                'ruta_imagen' => 'img/Moneda.png',
                'tipo' => 'tienda',
            ],
            [
                'nombre' => 'Consumicion gratis en Downtown',
                'descripcion' => 'Canjea una consumicion gratis en tu local colaborador.',
                'premium' => false,
                'puntos_necesarios' => 600,
                'nivel_necesario' => 1,
                'ruta_imagen' => 'img/Moneda.png',
                'tipo' => 'tienda',
            ],
            [
                'nombre' => 'Potenciador de puntos x2',
                'descripcion' => 'Duplica los puntos obtenidos en misiones durante 7 dias.',
                'premium' => false,
                'puntos_necesarios' => 1200,
                'nivel_necesario' => 1,
                'ruta_imagen' => 'img/Moneda.png',
                'tipo' => 'tienda',
            ],
            [
                'nombre' => '10EUR en Google Play',
                'descripcion' => 'Recibe un codigo de Google Play para canjear en tu cuenta.',
                'premium' => false,
                'puntos_necesarios' => 7599,
                'nivel_necesario' => 1,
                'ruta_imagen' => 'img/LogoUsarDiaDia.png',
                'tipo' => 'tienda',
            ],
        ];

        foreach ($productos as $producto) {
            Recompensa::firstOrCreate(
                [
                    'nombre' => $producto['nombre'],
                    'tipo' => 'tienda',
                ],
                $producto
            );
        }
    }
}
