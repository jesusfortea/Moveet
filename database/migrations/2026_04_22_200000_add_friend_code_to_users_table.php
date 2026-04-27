<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function generateUniqueFriendCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (DB::table('users')->where('friend_code', $code)->exists());

        return $code;
    }

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('friend_code', 6)->nullable()->after('ruta_imagen');
        });

        DB::table('users')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->each(function (object $user): void {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['friend_code' => $this->generateUniqueFriendCode()]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('friend_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['friend_code']);
            $table->dropColumn('friend_code');
        });
    }
};
