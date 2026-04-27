<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_usuario_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_usuario_id')->constrained('rutas_usuario')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->unsignedInteger('current_checkpoint_index')->default(0);
            $table->unsignedInteger('checkpoint_total')->default(0);
            $table->decimal('verification_threshold_meters', 6, 2)->default(40);
            $table->string('verification_token', 80)->unique();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->decimal('last_latitude', 10, 7)->nullable();
            $table->decimal('last_longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['ruta_usuario_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_usuario_attempts');
    }
};
