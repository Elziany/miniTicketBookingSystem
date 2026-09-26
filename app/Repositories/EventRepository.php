<?php

namespace App\Repositories;

use App\Enum\EventStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    public function getActiveEvents():Collection{
        $activeStatus = EventStatus::activeStatuses();
        return Event::whereIn('status', $activeStatus)->get();
    }   

    public function getEventById($eventId)
    {
        return Event::find($eventId);
    }
    public function cancelEvent($event){
        return $event->update([
            'status' => EventStatus::CANCELLED
        ]);
    }
}
