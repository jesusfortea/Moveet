<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logros', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nombre', 100);
            $table->string('descripcion', 255);
            $table->string('icono', 20)->default('badge');
            $table->unsignedInteger('puntos_bonus')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('user_logros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('logro_id')->constrained('logros')->cascadeOnDelete();
            $table->timestamp('achieved_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'logro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logros');
        Schema::dropIfExists('logros');
    }
};
