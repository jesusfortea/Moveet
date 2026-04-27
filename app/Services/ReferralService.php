<?php

namespace App\Services;

use App\Models\Referido;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    public function __construct(
        private PointsHistoryService $pointsHistoryService,
        private NotificationService $notificationService,
    ) {
    }

    public function linkIfValid(User $newUser, ?string $referralCode): void
    {
        if (!$referralCode) {
            return;
        }

        $referrer = User::query()
            ->where('referral_code', trim($referralCode))
            ->where('id', '!=', $newUser->id)
            ->first();

        if (!$referrer) {
            return;
        }

        DB::transaction(function () use ($newUser, $referrer) {
            $newUser->update(['referred_by_user_id' => $referrer->id]);

            Referido::firstOrCreate([
                'referrer_user_id' => $referrer->id,
                'referred_user_id' => $newUser->id,
            ], [
                'reward_points' => 500,
            ]);
        });
    }

    public function processFirstMissionReward(User $referredUser): void
    {
        $referral = Referido::query()
            ->where('referred_user_id', $referredUser->id)
            ->first();

        if (!$referral) {
            return;
        }

        if ($referral->rewarded_at) {
            return;
        }

        DB::transaction(function () use ($referral, $referredUser) {
            $points = (int) $referral->reward_points;

            $referrer = User::find($referral->referrer_user_id);
            if (!$referrer) {
                return;
            }

            $referredUser->increment('puntos', $points);
            $referrer->increment('puntos', $points);

            $this->pointsHistoryService->log(
                $referredUser,
                'referral',
                $points,
                'Bonus de referido por completar primera mision',
                $referrer->id
            );

            $this->pointsHistoryService->log(
                $referrer,
                'referral',
                $points,
                'Bonus por referido activo',
                $referredUser->id
            );

            $this->notificationService->notify(
                $referredUser->id,
                'referral',
                'Bonus de referido conseguido',
                'Has ganado ' . $points . ' puntos por tu primera mision como referido.',
                route('usuario.historial_puntos')
            );

            $this->notificationService->notify(
                $referrer->id,
                'referral',
                'Tu referido ha completado su primera mision',
                'Has ganado ' . $points . ' puntos de recompensa.',
                route('usuario.historial_puntos')
            );

            $referral->update([
                'first_mission_completed_at' => Carbon::now(),
                'rewarded_at' => Carbon::now(),
            ]);
        });
    }
}
