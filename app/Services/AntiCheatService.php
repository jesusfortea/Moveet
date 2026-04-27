<?php

namespace App\Services;

use App\Models\PuntosHistorial;
use App\Models\User;

class AntiCheatService
{
    // Velocidad máxima permitida para caminar: 50 km/h es sospechoso para caminata
    const MAX_WALKING_SPEED_KMH = 50;
    
    // Radio de precisión GPS aceptable: 50 metros
    const GPS_ACCURACY_THRESHOLD = 50;
    
    // Velocidad máxima para ciclismo/vehículos (pero esto no debería estar en misiones a pie)
    const MAX_VEHICLE_SPEED_KMH = 150;

    /**
     * Validar si la ubicación y velocidad son legítimas
     */
    public function validateLocationAndSpeed(
        float $latitude,
        float $longitude,
        float $speed_kmh,
        float $accuracy_meters = null,
        User $user = null
    ): array {
        $isValid = true;
        $alerts = [];

        // Validación de rango de coordenadas globales
        if ($latitude < -90 || $latitude > 90) {
            $isValid = false;
            $alerts[] = 'Latitud fuera de rango válido';
        }

        if ($longitude < -180 || $longitude > 180) {
            $isValid = false;
            $alerts[] = 'Longitud fuera de rango válido';
        }

        // Validación de velocidad
        if ($speed_kmh > self::MAX_WALKING_SPEED_KMH) {
            $isValid = false;
            $alerts[] = "Velocidad sospechosa: {$speed_kmh} km/h (máximo permitido: " . self::MAX_WALKING_SPEED_KMH . ")";
        }

        // Validación de precisión GPS
        if ($accuracy_meters !== null && $accuracy_meters > self::GPS_ACCURACY_THRESHOLD) {
            $isValid = false;
            $alerts[] = "Precisión GPS insuficiente: {$accuracy_meters}m (máximo: " . self::GPS_ACCURACY_THRESHOLD . "m)";
        }

        // Si el usuario existe, revisar cambios bruscos de ubicación
        if ($user && $user->last_location_latitude && $user->last_location_longitude) {
            $timeDiff = now()->diffInSeconds($user->last_location_timestamp ?? now());
            $distance = $this->haversineDistance(
                $user->last_location_latitude,
                $user->last_location_longitude,
                $latitude,
                $longitude
            );

            // Si se movió más de 200km en menos de 5 minutos = teleportación
            if ($distance > 200 && $timeDiff < 300) {
                $isValid = false;
                $alerts[] = "Teleportación detectada: {$distance}km en {$timeDiff}s";
            }
        }

        return [
            'valid' => $isValid,
            'alerts' => $alerts,
            'speed_kmh' => $speed_kmh,
            'accuracy_meters' => $accuracy_meters,
        ];
    }

    /**
     * Calcular distancia entre dos coordenadas (Haversine)
     */
    private function haversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Registrar intento sospechoso en el historial
     */
    public function logSuspiciousActivity(
        User $user,
        string $reason,
        float $latitude = null,
        float $longitude = null,
        float $speed_kmh = null
    ): void {
        PuntosHistorial::create([
            'user_id' => $user->id,
            'tipo' => 'admin_adjustment',
            'cantidad' => 0,
            'motivo' => "🚨 Actividad sospechosa: {$reason}",
            'velocidad_maxima' => $speed_kmh,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Validar patrón de actividad del usuario
     */
    public function checkActivityPattern(User $user): array {
        $alerts = [];

        // Revisar si el usuario gana más puntos de lo normal en poco tiempo
        $pointsLastHour = PuntosHistorial::where('user_id', $user->id)
            ->where('tipo', 'earned')
            ->where('created_at', '>=', now()->subHour())
            ->sum('cantidad');

        if ($pointsLastHour > 10000) {
            $alerts[] = "Ganancia anormal de puntos: {$pointsLastHour} en 1 hora";
        }

        // Revisar si completa misiones a una velocidad anómala
        $missionsLastHour = $user->misiones()
            ->wherePivot('completada_at', '>=', now()->subHour())
            ->count();

        if ($missionsLastHour > 10) {
            $alerts[] = "Demasiadas misiones completadas: {$missionsLastHour} en 1 hora";
        }

        return $alerts;
    }
}
