<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    protected $fillable = [
        'slug',
        'nombre',
        'descripcion',
        'icono',
        'puntos_bonus',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_logros')
            ->withPivot(['achieved_at'])
            ->withTimestamps();
    }
}
