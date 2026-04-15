<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('misiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->nullable()->constrained('eventos')->nullOnDelete();
            $table->string('nombre');
            $table->string('descripcion');
            $table->decimal('ejeX', 10, 7)->nullable();
            $table->decimal('ejeY', 10, 7)->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('premium')->default(false);
            $table->boolean('semanal')->default(false);
            $table->integer('puntos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('misiones');
    }
};