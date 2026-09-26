<?php

namespace App\Services;

use App\Events\CancellEvent;
use App\Repositories\EventRepository;
use Illuminate\Database\Eloquent\Collection;

class EventService
{

public function __construct(private EventRepository $eventRepository , private EventNotificationService $eventNotificationService)
    {
    }

    public function getActiveEvents(): Collection
    {
        return $this->eventRepository->getActiveEvents();
    }

    public function getEventById($eventId)
    {
        return $this->eventRepository->getEventById($eventId);
    }

    public function cancelEvent($event){
         $this->eventRepository->cancelEvent($event);
         CancellEvent::dispatch($event);
         return 1 ;
    }
	
}
