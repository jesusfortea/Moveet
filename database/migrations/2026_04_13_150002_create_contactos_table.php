<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solicitudes de amistad
        Schema::create('solicitudes_amistad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emisor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receptor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->timestamps();

            $table->unique(['emisor_id', 'receptor_id']);
        });

        // Contactos (amigos aceptados)
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contacto_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'contacto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos');
        Schema::dropIfExists('solicitudes_amistad');
    }
};
