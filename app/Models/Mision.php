<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mision extends Model
{
    protected $table = 'misiones';

    protected $fillable = [
        'evento_id', 'nombre', 'descripcion',
        'ejeX', 'ejeY', 'direccion', 'premium', 'semanal', 'puntos',
    ];

    protected $casts = [
        'premium' => 'boolean',
        'semanal'  => 'boolean',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_mision')
                    ->withPivot(['completada', 'fecha_asignacion', 'fecha_limite', 'fecha_completado'])
                    ->withTimestamps();
    }
}
