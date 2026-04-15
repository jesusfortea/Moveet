<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('daily_mission_cycle_end')->nullable()->after('ruta_imagen');
            $table->timestamp('weekly_mission_cycle_end')->nullable()->after('daily_mission_cycle_end');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_mission_cycle_end', 'weekly_mission_cycle_end']);
        });
    }
};
