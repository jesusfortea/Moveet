<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';

    protected $fillable = [
        'user_id',
        'titulo',
        'contenido',
        'respuesta',
        'respondida_por',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function respondidaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondida_por');
    }

    public function estaRespondida(): bool
    {
        return $this->estado === 'respondida';
    }

    public function marcarRespondida(User $admin, string $respuesta): void
    {
        $this->update([
            'respuesta' => $respuesta,
            'respondida_por' => $admin->id,
            'estado' => 'respondida',
        ]);
    }
}
