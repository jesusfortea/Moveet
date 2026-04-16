<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    protected $table = 'lugares';

    protected $fillable = [
        'nombre',
        'ejeX',
        'ejeY',
    ];

    protected $casts = [
        'ejeX' => 'float',
        'ejeY' => 'float',
    ];
}
