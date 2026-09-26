<?php

namespace App\Jobs;

use App\Services\EventNotificationService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPostEventFeedbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EventNotificationService $notificationService , ReservationService $reservationService): void
    {
        $reservations = $reservationService->getReservationsForFeedback();
        foreach ($reservations as $reservation) {
            $notificationService->notifyFeedbackRequest($reservation);
            $reservation->update([
                'feedback_requested_at' => Carbon::now(),
            ]);
        }
    }
}