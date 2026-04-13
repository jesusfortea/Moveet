<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recompensas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pase_de_paseo_id')->nullable()->constrained('pase_de_paseo')->nullOnDelete();
            $table->string('nombre');
            $table->string('descripcion');
            $table->boolean('premium')->default(false);
            $table->integer('puntos_necesarios');
            $table->integer('nivel_necesario');
            $table->string('ruta_imagen');
            $table->enum('tipo', ['tienda', 'pase_de_paseo'])->default('tienda');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recompensas');
    }
};
