<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'dni', 'nacimiento',
        'telefono', 'phone', 'premium', 'nivel', 'puntos', 'ruta_imagen', 'birth_date', 'is_admin',
    ];

    protected $attributes = [
        'puntos' => 0,
        'nivel' => 1,
        'premium' => false,
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'      => 'datetime',
        'nacimiento'             => 'date',
        'premium'                => 'boolean',
        'is_admin'               => 'boolean',
        'password'               => 'hashed',
        'daily_mission_cycle_end'   => 'datetime',
        'weekly_mission_cycle_end'  => 'datetime',
    ];

    // ── Misiones ────────────────────────────────────────────────
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

    // ── Contactos ───────────────────────────────────────────────
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

    // ── Pase de paseo ───────────────────────────────────────────
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

    // ── Inventario ──────────────────────────────────────────────
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

    // ── Tienda ──────────────────────────────────────────────────
    public function comprasTienda()
    {
        return $this->hasMany(CompraTienda::class);
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
}
