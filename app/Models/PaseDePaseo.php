<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaseDePaseo extends Model
{
    protected $table = 'pase_de_paseo';

    protected $fillable = ['nombre', 'ruta_imagen'];

    public function recompensas()
    {
        return $this->hasMany(Recompensa::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_pase_de_paseo')
                    ->withPivot(['nivel_actual', 'fecha_inicio', 'fecha_fin'])
                    ->withTimestamps();
    }
}
