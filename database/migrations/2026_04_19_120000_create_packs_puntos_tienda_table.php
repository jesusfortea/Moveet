<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packs_puntos_tienda', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedInteger('puntos');
            $table->decimal('precio_euros', 8, 2);
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->string('ruta_imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packs_puntos_tienda');
    }
};
