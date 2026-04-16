<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarjetas_bancarias', function (Blueprint $table) {
            $table->string('fecha_caducidad', 5)->nullable()->after('numero_enmascarado');
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas_bancarias', function (Blueprint $table) {
            $table->dropColumn('fecha_caducidad');
        });
    }
};
