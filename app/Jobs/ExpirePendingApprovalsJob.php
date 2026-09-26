<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\ReservationNotificationService;
use App\Services\ReservationPushNotificationService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExpirePendingApprovalsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ReservationNotificationService $notificationService , ReservationService $reservationService): void
    {

        $expiredReservations = $reservationService->getReservationReadyForExpiration();
        foreach ($expiredReservations as $reservation) {
            DB::transaction(function () use ($reservation, $notificationService) {
                $reservation->update(['status' => 'expired']);
                $notificationService->notifyExpired($reservation);
            });
        }
    }
}