<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referido extends Model
{
    protected $table = 'referidos';

    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'first_mission_completed_at',
        'rewarded_at',
        'reward_points',
    ];

    protected $casts = [
        'first_mission_completed_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
