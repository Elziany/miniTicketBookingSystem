<?php

namespace App\Listeners;

use App\Events\CancellEvent;
use App\Services\EventNotificationService;
use App\Services\ReservationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEventCancellationNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private EventNotificationService $eventNotificationService , private ReservationService $reservationService
    ) {}

    public function handle(CancellEvent $event): void
    {
        $reservatoins = $this->reservationService->getReservationsByEvent($event->event->id);

            $reservatoins->chunk(100, function ($reservations) use ($event) {
                foreach ($reservations as $reservation) {
                    $this->eventNotificationService->notifyCancelled(
                        $reservation,
                    );
                }
            });
    }
}