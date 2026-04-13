<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraTienda extends Model
{
    protected $table = 'compras_tienda';

    protected $fillable = ['user_id', 'recompensa_id', 'puntos_gastados'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function recompensa()
    {
        return $this->belongsTo(Recompensa::class);
    }
}
