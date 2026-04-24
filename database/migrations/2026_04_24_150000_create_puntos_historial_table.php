<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puntos_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo'); // earned, spent, reward, mission, store, referral, admin_adjustment
            $table->integer('cantidad');
            $table->string('motivo')->nullable(); // "Misión completada: Caminar 5km", "Compra: Recompensa X", etc.
            $table->foreignId('related_user_id')->nullable()->constrained('users')->nullOnDelete(); // Para referidos
            $table->string('related_model')->nullable(); // 'Mision', 'Recompensa', 'RutaUsuario', etc.
            $table->unsignedBigInteger('related_model_id')->nullable();
            $table->decimal('velocidad_maxima', 5, 2)->nullable(); // Para anti-cheat: velocidad del GPS
            $table->decimal('distancia_registrada', 8, 2)->nullable(); // Distancia en km
            $table->string('ip_address')->nullable(); // Para auditoría
            $table->string('user_agent')->nullable(); // Para auditoría
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['tipo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntos_historial');
    }
};
