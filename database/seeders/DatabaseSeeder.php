<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear admin
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'dni' => '12345678A',
            'nacimiento' => '1990-01-01',
            'telefono' => '600000000',
            'password' => bcrypt('password123'),
            'puntos' => 0,
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'dni' => '11223344',
            'nacimiento' => '1992-10-20',
            'telefono' => '622222222',
            'puntos' => 150,
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Usuario Nivel 3',
            'email' => 'nivel3@example.com',
            'dni' => '11224455',
            'nacimiento' => '1995-01-01',
            'telefono' => '633333333',
            'puntos' => 500,
            'nivel' => 3,
            'premium' => true,
            'password' => 'password',
        ]);

        // Llamar a seeders
        $this->call(EventoSeeder::class);
        $this->call(MisionSeeder::class);
        $this->call(PaseDePaseoSeeder::class);
    }
}
