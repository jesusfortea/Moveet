<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarjetaBancaria extends Model
{
    protected $table = 'tarjetas_bancarias';

    protected $fillable = [
        'user_id', 'titular', 'numero_enmascarado', 'token_pago', 'marca',
    ];

    // Nunca exponer el token de pago en respuestas JSON
    protected $hidden = ['token_pago'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
