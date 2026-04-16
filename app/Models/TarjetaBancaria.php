<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TarjetaBancaria extends Model
{
    protected $table = 'tarjetas_bancarias';

    protected $fillable = [
        'user_id', 'titular', 'numero_enmascarado', 'fecha_caducidad', 'token_pago', 'marca',
    ];

    // Nunca exponer el token de pago en respuestas JSON
    protected $hidden = ['token_pago'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function getEstaCaducadaAttribute(): bool
    {
        if (!$this->fecha_caducidad) {
            return false;
        }

        try {
            $expira = Carbon::createFromFormat('m/y', $this->fecha_caducidad)->endOfMonth();
            return $expira->lt(now()->startOfDay());
        } catch (\Throwable $e) {
            return false;
        }
    }
}
