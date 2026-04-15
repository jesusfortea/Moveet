<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventoController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? User::first();
        $now = Carbon::now();

        $evento = Evento::whereDate('fecha_inicio', '<=', $now)
            ->whereDate('fecha_fin', '>=', $now)
            ->with('misiones')
            ->first();

        $misiones = $evento
            ? $evento->misiones->map(function ($mision) {
                return [
                    'id' => $mision->id,
                    'nombre' => $mision->nombre,
                    'descripcion' => $mision->descripcion,
                    'puntos' => $mision->puntos,
                    'semanal' => $mision->semanal,
                    'premium' => $mision->premium,
                    'completada' => false,
                    'ejeX' => $mision->ejeX,
                    'ejeY' => $mision->ejeY,
                    'direccion' => $mision->direccion,
                    'metros_requeridos' => $mision->metros_requeridos,
                ];
            })->toArray()
            : [];

        return view('eventos', [
            'evento' => $evento ? [
                'id' => $evento->id,
                'nombre' => $evento->nombre,
                'descripcion' => $evento->descripcion,
                'direccion' => $evento->direccion,
                'fecha_inicio' => $evento->fecha_inicio->toDateString(),
                'fecha_fin' => $evento->fecha_fin->toDateString(),
            ] : null,
            'misiones' => $misiones,
            'fechaLimiteDiarias' => $now->copy()->addDay()->toISOString(),
            'fechaLimiteSemanales' => $now->copy()->addDays(7)->toISOString(),
            'fechaFinEvento' => $evento ? $evento->fecha_fin->toISOString() : null,
        ]);
    }
}
