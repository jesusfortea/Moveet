<?php

namespace App\Services;

use App\Models\PuntosHistorial;
use App\Models\User;

class PointsHistoryService
{
    public function log(
        User $user,
        string $tipo,
        int $cantidad,
        string $motivo,
        ?int $relatedUserId = null,
        ?float $velocidadMaxima = null,
        ?float $distanciaRegistrada = null
    ): void {
        PuntosHistorial::create([
            'user_id' => $user->id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'related_user_id' => $relatedUserId,
            'velocidad_maxima' => $velocidadMaxima,
            'distancia_registrada' => $distanciaRegistrada,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
