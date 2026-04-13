<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudAmistad extends Model
{
    protected $table = 'solicitudes_amistad';

    protected $fillable = ['emisor_id', 'receptor_id', 'estado'];

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
}
