<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('first_mission_completed_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->unsignedInteger('reward_points')->default(500);
            $table->timestamps();

            $table->unique(['referrer_user_id', 'referred_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referidos');
    }
};
