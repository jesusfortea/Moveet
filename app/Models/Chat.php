<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['contacto_id'];

    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    public function ultimoMensaje()
    {
        return $this->hasOne(Mensaje::class)->latestOfMany();
    }
}
