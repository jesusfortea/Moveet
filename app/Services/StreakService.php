<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StreakService
{
    private const PREMIUM_FREEZES_PER_MONTH = 2;
    private const FREEZE_COST_POINTS = 250;

    public function syncStreakState(User $user): void
    {
        $today = Carbon::today();

        $this->grantMonthlyPremiumFreezes($user, $today);

        if (!$user->streak_last_activity_date) {
            return;
        }

        $lastActivity = Carbon::parse($user->streak_last_activity_date)->startOfDay();

        if ($lastActivity->gte($today)) {
            return;
        }

        $missedDays = $lastActivity->diffInDays($today) - 1;

        if ($missedDays <= 0) {
            return;
        }

        $availableFreezes = (int) $user->streak_freezes;
        $freezesToConsume = min($availableFreezes, $missedDays);

        if ($freezesToConsume > 0) {
            $user->streak_freezes = $availableFreezes - $freezesToConsume;
        }

        $remainingMisses = $missedDays - $freezesToConsume;

        if ($remainingMisses > 0) {
            $user->current_streak = 0;
        } elseif ($missedDays > 0) {
            $user->streak_last_activity_date = $today->copy()->subDay()->toDateString();
        }

        $user->save();
    }

    public function registerWalkActivity(User $user): void
    {
        $this->syncStreakState($user);

        $today = Carbon::today();

        if (!$user->streak_last_activity_date) {
            $user->current_streak = 1;
            $user->longest_streak = max((int) $user->longest_streak, 1);
            $user->streak_last_activity_date = $today->toDateString();
            $user->save();
            return;
        }

        $lastActivity = Carbon::parse($user->streak_last_activity_date)->startOfDay();

        if ($lastActivity->isSameDay($today)) {
            return;
        }

        if ($lastActivity->isSameDay($today->copy()->subDay())) {
            $user->current_streak = (int) $user->current_streak + 1;
        } else {
            $user->current_streak = 1;
        }

        $user->longest_streak = max((int) $user->longest_streak, (int) $user->current_streak);
        $user->streak_last_activity_date = $today->toDateString();
        $user->save();
    }

    public function buyFreeze(User $user): bool
    {
        if ((int) $user->puntos < self::FREEZE_COST_POINTS) {
            return false;
        }

        DB::transaction(function () use ($user) {
            $user->decrement('puntos', self::FREEZE_COST_POINTS);
            $user->increment('streak_freezes');
        });

        return true;
    }

    public function freezeCost(): int
    {
        return self::FREEZE_COST_POINTS;
    }

    private function grantMonthlyPremiumFreezes(User $user, Carbon $today): void
    {
        if (!(bool) $user->premium) {
            return;
        }

        $currentMonth = $today->format('Y-m');

        if ($user->streak_premium_month === $currentMonth) {
            return;
        }

        $user->streak_freezes = (int) $user->streak_freezes + self::PREMIUM_FREEZES_PER_MONTH;
        $user->streak_premium_month = $currentMonth;
        $user->save();
    }
}
