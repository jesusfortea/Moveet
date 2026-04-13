<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pase de paseo del usuario (uno activo por usuario en cada temporada)
        Schema::create('user_pase_de_paseo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pase_de_paseo_id')->constrained('pase_de_paseo')->cascadeOnDelete();
            $table->integer('nivel_actual')->default(1);
            $table->timestamp('fecha_inicio')->useCurrent();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'pase_de_paseo_id']);
        });

        // Compras en la tienda (log de transacciones)
        Schema::create('compras_tienda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recompensa_id')->constrained('recompensas')->cascadeOnDelete();
            $table->integer('puntos_gastados');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras_tienda');
        Schema::dropIfExists('user_pase_de_paseo');
    }
};
