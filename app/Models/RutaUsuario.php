<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaUsuario extends Model
{
    protected $table = 'rutas_usuario';

    protected $fillable = [
        'creator_user_id',
        'titulo',
        'descripcion',
        'dificultad',
        'distancia_metros',
        'puntos_recompensa',
        'ruta_geojson',
        'min_nivel',
        'premium_only',
        'publicado',
        'activo',
        'rating_promedio',
        'rating_count',
        'completadas_count',
        'puntos_generados',
    ];

    protected $casts = [
        'ruta_geojson' => 'array',
        'premium_only' => 'boolean',
        'publicado' => 'boolean',
        'activo' => 'boolean',
        'rating_promedio' => 'float',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function completions()
    {
        return $this->hasMany(RutaUsuarioCompletion::class, 'ruta_usuario_id');
    }

    public function ratings()
    {
        return $this->hasMany(RutaUsuarioRating::class, 'ruta_usuario_id');
    }

    public function attempts()
    {
        return $this->hasMany(RutaUsuarioAttempt::class, 'ruta_usuario_id');
    }
}
