<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_mision', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mision_id')->constrained('misiones')->cascadeOnDelete();
            $table->boolean('completada')->default(false);
            $table->timestamp('fecha_asignacion');
            $table->timestamp('fecha_limite');   // +24h si diaria, +7d si semanal
            $table->timestamp('fecha_completado')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'mision_id', 'fecha_asignacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_mision');
    }
};
