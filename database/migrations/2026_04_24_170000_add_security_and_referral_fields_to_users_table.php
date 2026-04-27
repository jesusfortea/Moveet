<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code', 16)->nullable()->unique()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'referred_by_user_id')) {
                $table->foreignId('referred_by_user_id')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'last_location_latitude')) {
                $table->decimal('last_location_latitude', 10, 7)->nullable()->after('referred_by_user_id');
                $table->decimal('last_location_longitude', 10, 7)->nullable()->after('last_location_latitude');
                $table->timestamp('last_location_timestamp')->nullable()->after('last_location_longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referred_by_user_id')) {
                $table->dropConstrainedForeignId('referred_by_user_id');
            }
            $table->dropColumn([
                'referral_code',
                'last_location_latitude',
                'last_location_longitude',
                'last_location_timestamp',
            ]);
        });
    }
};
