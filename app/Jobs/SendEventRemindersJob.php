<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\EventNotificationService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EventNotificationService $notificationService , ReservationService $reservationService): void
    {
        $now = Carbon::now();

        $reminderConfig = [
            '1_day'      => ['minutes' => 1440],
            '1_hour'     => ['minutes' => 60],
            '10_minutes' => ['minutes' => 10],
        ];

        foreach ($reminderConfig as $timeframe => $config) {
            $targetTime = $now->copy()->addMinutes($config['minutes']);
            $reservations = $reservationService->getReservationsForReminder($timeframe , $targetTime);
            foreach ($reservations as $reservation) {
                $notificationService->notifyReminder($reservation, $timeframe);
                $reservation->reminders()->create([
                    'timeframe' => $timeframe,
                    'sent_at' => Carbon::now(),
                ]);
            }
        }
    }
}