<?php

namespace App\Services;

use App\Models\User;

class LevelService
{
    /**
     * Calcula la experiencia necesaria para subir al siguiente nivel.
     * Fórmula: 100 * nivel^1.5
     */
    public function experienceForLevel(int $level): int
    {
        return (int) (100 * pow($level, 1.5));
    }

    /**
     * Añade experiencia al usuario y comprueba si sube de nivel.
     */
    public function addExperience(User $user, int $amount): array
    {
        $user->increment('experiencia', $amount);
        $user = $user->fresh();

        $leveledUp = false;
        $levelsGained = 0;

        while ($user->experiencia >= $this->experienceForLevel($user->nivel)) {
            $user->increment('nivel');
            $user = $user->fresh();
            $leveledUp = true;
            $levelsGained++;
        }

        return [
            'leveled_up' => $leveledUp,
            'levels_gained' => $levelsGained,
            'current_level' => $user->nivel,
            'current_exp' => $user->experiencia,
            'next_level_exp' => $this->experienceForLevel($user->nivel),
        ];
    }
}
