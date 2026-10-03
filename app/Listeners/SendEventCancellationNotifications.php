<?php

namespace App\Listeners;

use App\Enum\ReservationStatus;
use App\Events\CancellEvent;
use App\Models\Reservation;
use App\Services\EventNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEventCancellationNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private EventNotificationService $eventNotificationService
    ) {}

    public function handle(CancellEvent $event): void
    {
        Reservation::query()
            ->where('event_id', $event->event->id)
            ->where('status', ReservationStatus::CANCELLED)
            ->with(['user', 'event'])
            ->chunkById(100, function ($reservations) use ($event) {
                foreach ($reservations as $reservation) {
                    $this->eventNotificationService->notifyCancelled(
                        $reservation,
                        $event->event->cancellation_reason
                    );
                }
            });
    }
}
