<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('dificultad', ['facil', 'media', 'dificil'])->default('media');
            $table->unsignedInteger('distancia_metros');
            $table->unsignedInteger('puntos_recompensa')->default(50);
            $table->json('ruta_geojson');
            $table->unsignedTinyInteger('min_nivel')->default(1);
            $table->boolean('premium_only')->default(false);
            $table->boolean('publicado')->default(true);
            $table->boolean('activo')->default(true);
            $table->decimal('rating_promedio', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('completadas_count')->default(0);
            $table->unsignedInteger('puntos_generados')->default(0);
            $table->timestamps();
        });

        Schema::create('ruta_usuario_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_usuario_id')->constrained('rutas_usuario')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('puntos_otorgados');
            $table->timestamp('completada_en')->useCurrent();
            $table->unsignedInteger('creator_reward_points')->default(0);
            $table->timestamp('creator_rewarded_at')->nullable();
            $table->timestamps();

            $table->unique(['ruta_usuario_id', 'user_id']);
        });

        Schema::create('ruta_usuario_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_usuario_id')->constrained('rutas_usuario')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('estrellas');
            $table->string('comentario', 500)->nullable();
            $table->timestamps();

            $table->unique(['ruta_usuario_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_usuario_ratings');
        Schema::dropIfExists('ruta_usuario_completions');
        Schema::dropIfExists('rutas_usuario');
    }
};
