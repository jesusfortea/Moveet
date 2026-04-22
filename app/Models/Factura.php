<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'importe',
        'concepto',
        'nombre_titular',
        'email_titular',
        'ultimos_digitos'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
