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

        if (!$evento) {
            return view('eventos', [
                'evento'              => null,
                'misiones'            => [],
                'fechaLimiteDiarias'  => $now->copy()->addDay()->toISOString(),
                'fechaLimiteSemanales'=> $now->copy()->addDays(7)->toISOString(),
                'fechaFinEvento'      => null,
            ]);
        }

        // Asignar misiones de evento al usuario si aún no están en user_mision
        if ($user) {
            $fechaLimiteEvento = Carbon::parse($evento->fecha_fin)->endOfDay();
            $misionIdsAsignadas = $user->misiones()
                ->wherePivot('fecha_limite', '>=', $now)
                ->pluck('misiones.id')
                ->all();

            foreach ($evento->misiones as $mision) {
                if (!in_array($mision->id, $misionIdsAsignadas)) {
                    $user->misiones()->attach($mision->id, [
                        'completada'      => false,
                        'fecha_asignacion'=> $now,
                        'fecha_limite'    => $fechaLimiteEvento,
                    ]);
                }
            }
        }

        // Obtener estado real de completado del usuario para las misiones del evento
        $misionesConEstado = $user
            ? $user->misiones()
                ->whereIn('misiones.id', $evento->misiones->pluck('id'))
                ->get()
                ->keyBy('id')
            : collect();

        $misiones = $evento->misiones->map(function ($mision) use ($misionesConEstado) {
            $pivot = optional($misionesConEstado->get($mision->id))->pivot;
            return [
                'id'               => $mision->id,
                'nombre'           => $mision->nombre,
                'descripcion'      => $mision->descripcion,
                'puntos'           => $mision->puntos,
                'semanal'          => $mision->semanal,
                'premium'          => $mision->premium,
                'completada'       => $pivot ? (bool) $pivot->completada : false,
                'ejeX'             => $mision->ejeX,
                'ejeY'             => $mision->ejeY,
                'direccion'        => $mision->direccion,
                'metros_requeridos'=> $mision->metros_requeridos,
            ];
        })->toArray();

        return view('eventos', [
            'evento' => [
                'id'          => $evento->id,
                'nombre'      => $evento->nombre,
                'descripcion' => $evento->descripcion,
                'direccion'   => $evento->direccion,
                'fecha_inicio'=> $evento->fecha_inicio->toDateString(),
                'fecha_fin'   => $evento->fecha_fin->toDateString(),
            ],
            'misiones'            => $misiones,
            'fechaLimiteDiarias'  => $now->copy()->addDay()->toISOString(),
            'fechaLimiteSemanales'=> $now->copy()->addDays(7)->toISOString(),
            'fechaFinEvento'      => $evento->fecha_fin->toISOString(),
        ]);
    }
}
