<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('misiones', function (Blueprint $table) {
            $table->integer('metros_requeridos')->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('misiones', function (Blueprint $table) {
            $table->dropColumn('metros_requeridos');
        });
    }
};