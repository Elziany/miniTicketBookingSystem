<?php
namespace App\Listeners;

use App\Events\EventRescheduled;
use App\Services\EventNotificationService;
use App\Services\ReservationRemindersService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleEventRescheduled implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private ReservationRemindersService $remindersService,
        private EventNotificationService $eventNotificationService
    ) {}

    public function handle(EventRescheduled $event): void
    {
        $eventModel = $event->event;

        $this->remindersService->restRemindersForEvent($eventModel->id);
        $eventModel->reservations()
            ->whereIn('status', ['confirmed', 'pending_approval'])
            ->with(['user'])
            ->chunk(100, function ($reservations) use ($eventModel) {
                foreach ($reservations as $reservation) {
                    $this->eventNotificationService->notifyRescheduled(
                        $reservation,
                        $eventModel
                    );
                }
            });
    }
}