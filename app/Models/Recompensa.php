<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    protected $fillable = [
        'pase_de_paseo_id', 'nombre', 'descripcion',
        'premium', 'puntos_necesarios', 'nivel_necesario',
        'ruta_imagen', 'tipo', 'visible_en_tienda',
    ];

    protected $casts = [
        'premium' => 'boolean',
        'visible_en_tienda' => 'boolean',
    ];

    public function paseDePaseo()
    {
        return $this->belongsTo(PaseDePaseo::class);
    }

    public function usuariosConRecompensa()
    {
        return $this->belongsToMany(User::class, 'inventario')
                    ->withPivot(['origen', 'obtenida_at'])
                    ->withTimestamps();
    }

    public function compras()
    {
        return $this->hasMany(CompraTienda::class);
    }
}
