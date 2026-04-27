<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaUsuarioRating extends Model
{
    protected $table = 'ruta_usuario_ratings';

    protected $fillable = [
        'ruta_usuario_id',
        'user_id',
        'estrellas',
        'comentario',
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
