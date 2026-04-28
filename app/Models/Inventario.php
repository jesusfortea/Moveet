<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';

    protected $fillable = ['user_id', 'recompensa_id', 'origen', 'obtenida_at', 'usado_at'];

    protected $casts = [
        'obtenida_at' => 'datetime',
        'usado_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function recompensa()
    {
        return $this->belongsTo(Recompensa::class);
    }
}
