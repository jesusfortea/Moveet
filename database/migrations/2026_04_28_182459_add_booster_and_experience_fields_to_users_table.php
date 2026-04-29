<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('experiencia')->default(0)->after('puntos');
            $table->timestamp('points_booster_until')->nullable()->after('premium_until');
            $table->timestamp('exp_booster_until')->nullable()->after('points_booster_until');
            $table->integer('free_mission_changes')->default(0)->after('exp_booster_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['experiencia', 'points_booster_until', 'exp_booster_until', 'free_mission_changes']);
        });
    }
};
