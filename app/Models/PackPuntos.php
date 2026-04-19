<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackPuntos extends Model
{
    protected $table = 'packs_puntos_tienda';

    protected $fillable = [
        'nombre',
        'puntos',
        'precio_euros',
        'destacado',
        'activo',
        'orden',
        'ruta_imagen',
    ];

    protected $casts = [
        'precio_euros' => 'decimal:2',
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'puntos' => 'integer',
        'orden' => 'integer',
    ];
}
