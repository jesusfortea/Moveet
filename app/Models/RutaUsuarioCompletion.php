<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaUsuarioCompletion extends Model
{
    protected $table = 'ruta_usuario_completions';

    protected $fillable = [
        'ruta_usuario_id',
        'user_id',
        'puntos_otorgados',
        'completada_en',
        'creator_reward_points',
        'creator_rewarded_at',
    ];

    protected $casts = [
        'completada_en' => 'datetime',
        'creator_rewarded_at' => 'datetime',
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
