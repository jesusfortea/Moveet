<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tarjeta bancaria del usuario (una por usuario)
        Schema::create('tarjetas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('titular');
            $table->string('numero_enmascarado', 19); // últimos 4 dígitos visibles: **** **** **** 1234
            $table->timestamps();
        });

        // Inventario: recompensas que ha desbloqueado/ganado el usuario
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recompensa_id')->constrained('recompensas')->cascadeOnDelete();
            $table->string('origen')->nullable(); // 'tienda', 'pase_de_paseo', 'mision'
            $table->timestamp('obtenida_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
        Schema::dropIfExists('tarjetas_bancarias');
    }
};
