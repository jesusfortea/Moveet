<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'dni',
        'nacimiento',
        'telefono',
        'phone',
        'premium',
        'premium_until',
        'nivel',
        'puntos',
        'ruta_imagen',
        'birth_date',
        'is_admin',
        'friend_code',
        'current_streak',
        'longest_streak',
        'streak_last_activity_date',
        'streak_freezes',
        'streak_premium_month',
        'referral_code',
        'referred_by_user_id',
        'last_location_latitude',
        'last_location_longitude',
        'last_location_timestamp',
        'is_blocked',
        'blocked_at',
        'blocked_reason',
    ];

    protected $attributes = [
        'puntos' => 0,
        'nivel' => 1,
        'premium' => false,
        'current_streak' => 0,
        'longest_streak' => 0,
        'streak_freezes' => 0,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'nacimiento' => 'date',
        'premium' => 'boolean',
        'is_admin' => 'boolean',
        'is_blocked' => 'boolean',
        'password' => 'hashed',
        'daily_mission_cycle_end' => 'datetime',
        'weekly_mission_cycle_end' => 'datetime',
        'streak_last_activity_date' => 'date',
        'last_location_timestamp' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    public function getPremiumAttribute($value)
    {
        if ($value && $this->premium_until) {
            return now()->lessThanOrEqualTo($this->premium_until);
        }

        return (bool) $value;
    }

    public function misiones()
    {
        return $this->belongsToMany(Mision::class, 'user_mision')
            ->withPivot(['completada', 'fecha_asignacion', 'fecha_limite', 'fecha_completado'])
            ->withTimestamps();
    }

    public function misionesDiarias()
    {
        return $this->misiones()->where('semanal', false);
    }

    public function misionesSemanales()
    {
        return $this->misiones()->where('semanal', true);
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    public function solicitudesEnviadas()
    {
        return $this->hasMany(SolicitudAmistad::class, 'emisor_id');
    }

    public function solicitudesRecibidas()
    {
        return $this->hasMany(SolicitudAmistad::class, 'receptor_id');
    }

    public function puntosHistorial()
    {
        return $this->hasMany(PuntosHistorial::class);
    }

    public function notificacionesUsuario()
    {
        return $this->hasMany(UserNotification::class)->latest();
    }

    public function logros()
    {
        return $this->belongsToMany(Logro::class, 'user_logros')
            ->withPivot(['achieved_at'])
            ->withTimestamps();
    }

    public function referidoPor()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function referidos()
    {
        return $this->hasMany(Referido::class, 'referrer_user_id');
    }

    public function reporteEnviados()
    {
        return $this->hasMany(ReporteContenido::class, 'reporter_user_id');
    }

    public function reporteRecibidos()
    {
        return $this->hasMany(ReporteContenido::class, 'reported_user_id');
    }

    public function pasesDePaseo()
    {
        return $this->belongsToMany(PaseDePaseo::class, 'user_pase_de_paseo')
            ->withPivot(['nivel_actual', 'fecha_inicio', 'fecha_fin'])
            ->withTimestamps();
    }

    public function paseActivo()
    {
        return $this->pasesDePaseo()->wherePivotNull('fecha_fin')->latest('pivot_fecha_inicio');
    }

    public function inventario()
    {
        return $this->hasMany(Inventario::class);
    }

    public function recompensas()
    {
        return $this->belongsToMany(Recompensa::class, 'inventario')
            ->withPivot(['origen', 'obtenida_at'])
            ->withTimestamps();
    }

    public function comprasTienda()
    {
        return $this->hasMany(CompraTienda::class);
    }

    public function rutasCreadas()
    {
        return $this->hasMany(RutaUsuario::class, 'creator_user_id');
    }

    public function rutasCompletadas()
    {
        return $this->hasMany(RutaUsuarioCompletion::class, 'user_id');
    }

    public function rutasValoradas()
    {
        return $this->hasMany(RutaUsuarioRating::class, 'user_id');
    }

    public function getRutaImagenUrlAttribute(): ?string
    {
        if (!$this->ruta_imagen) {
            return null;
        }

        if (Str::startsWith($this->ruta_imagen, ['http://', 'https://'])) {
            return $this->ruta_imagen;
        }

        if (Str::startsWith($this->ruta_imagen, 'storage/')) {
            return asset($this->ruta_imagen);
        }

        if (Storage::disk('public')->exists($this->ruta_imagen)) {
            return asset('storage/' . $this->ruta_imagen);
        }

        return asset(ltrim($this->ruta_imagen, '/'));
    }

    public function ensureReferralCode(): void
    {
        if (!empty($this->referral_code)) {
            return;
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (self::query()->where('referral_code', $code)->exists());

        $this->forceFill(['referral_code' => $code])->save();
    }
}
