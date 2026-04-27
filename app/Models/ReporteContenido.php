<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteContenido extends Model
{
    protected $table = 'reportes_contenido';

    protected $fillable = [
        'reporter_user_id',
        'reported_user_id',
        'target_type',
        'target_id',
        'reason',
        'details',
        'status',
        'resolved_by_user_id',
        'resolved_at',
        'resolution_note',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }
}
