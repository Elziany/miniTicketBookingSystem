<?php

namespace App\Jobs;

use App\Services\ReservationNotificationService;
use App\Services\ReservationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExpirePendingApprovalsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        ReservationNotificationService $notificationService,
        ReservationService $reservationService
    ): void {
        $expiredReservations = $reservationService->getReservationReadyForExpiration();

        foreach ($expiredReservations as $reservation) {
            DB::transaction(function () use ($reservation, $notificationService, $reservationService) {
                $reservationService->expireReservation($reservation, $notificationService);
            });
        }
    }
}
