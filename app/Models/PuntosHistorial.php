<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntosHistorial extends Model
{
    protected $table = 'puntos_historial';

    protected $fillable = [
        'user_id',
        'tipo',
        'cantidad',
        'motivo',
        'related_user_id',
        'related_model',
        'related_model_id',
        'velocidad_maxima',
        'distancia_registrada',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'velocidad_maxima' => 'float',
        'distancia_registrada' => 'float',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usuarioRelacionado()
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }
}
