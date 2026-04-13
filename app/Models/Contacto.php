<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable = ['user_id', 'contacto_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function amigo()
    {
        return $this->belongsTo(User::class, 'contacto_id');
    }

    public function chat()
    {
        return $this->hasOne(Chat::class);
    }
}
