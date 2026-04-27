<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaUsuarioAttempt extends Model
{
    protected $table = 'ruta_usuario_attempts';

    protected $fillable = [
        'ruta_usuario_id',
        'user_id',
        'status',
        'current_checkpoint_index',
        'checkpoint_total',
        'verification_threshold_meters',
        'verification_token',
        'started_at',
        'completed_at',
        'last_verified_at',
        'last_latitude',
        'last_longitude',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'last_latitude' => 'float',
        'last_longitude' => 'float',
        'verification_threshold_meters' => 'float',
    ];

    public function ruta()
    {
        return $this->belongsTo(RutaUsuario::class, 'ruta_usuario_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
