<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('puntos_historial')
            ->where('tipo', 'store')
            ->update(['tipo' => 'spent']);

        DB::statement("UPDATE puntos_historial SET cantidad = -ABS(cantidad) WHERE tipo = 'spent' AND cantidad > 0");
    }

    public function down(): void
    {
        DB::statement("UPDATE puntos_historial SET cantidad = ABS(cantidad) WHERE tipo = 'spent' AND motivo LIKE 'Compra en tienda:%'");
        DB::table('puntos_historial')
            ->where('tipo', 'spent')
            ->where('motivo', 'like', 'Compra en tienda:%')
            ->update(['tipo' => 'store']);
    }
};
