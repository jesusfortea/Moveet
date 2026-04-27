<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('current_streak')->default(0)->after('puntos');
            $table->unsignedInteger('longest_streak')->default(0)->after('current_streak');
            $table->date('streak_last_activity_date')->nullable()->after('longest_streak');
            $table->unsignedInteger('streak_freezes')->default(0)->after('streak_last_activity_date');
            $table->string('streak_premium_month', 7)->nullable()->after('streak_freezes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'current_streak',
                'longest_streak',
                'streak_last_activity_date',
                'streak_freezes',
                'streak_premium_month',
            ]);
        });
    }
};
