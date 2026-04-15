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

        // Crear usuarios de prueba
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'dni' => '12345678',
            'nacimiento' => '1990-01-01',
            'telefono' => '600000000',
            'puntos' => 100,
        ]);

        User::factory()->create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'dni' => '87654321',
            'nacimiento' => '1985-05-15',
            'telefono' => '611111111',
            'puntos' => 200,
        ]);

        User::factory()->create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'dni' => '11223344',
            'nacimiento' => '1992-10-20',
            'telefono' => '622222222',
            'puntos' => 150,
        ]);

        // Llamar a seeders
        $this->call(MisionSeeder::class);
    }
}
