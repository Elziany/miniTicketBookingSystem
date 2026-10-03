<?php

namespace App\Listeners;

use App\Enum\ReservationStatus;
use App\Events\EventRescheduled;
use App\Services\EventNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleEventRescheduled implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private EventNotificationService $eventNotificationService
    ) {}

    public function handle(EventRescheduled $event): void
    {
        $eventModel = $event->event;

        $eventModel->reservations()
            ->where('status', ReservationStatus::CONFIRMED)
            ->with(['user'])
            ->chunkById(100, function ($reservations) use ($eventModel) {
                foreach ($reservations as $reservation) {
                    $this->eventNotificationService->notifyRescheduled(
                        $reservation,
                        $eventModel
                    );
                }
            });
    }
}
