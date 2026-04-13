<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'ejeX', 'ejeY',
        'direccion', 'fecha_inicio', 'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function misiones()
    {
        return $this->hasMany(Mision::class);
    }
}
