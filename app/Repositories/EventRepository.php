<?php

namespace App\Repositories;

use App\Enum\EventStatus;
use App\Enum\ReservationStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    public function getActiveEvents(): Collection
    {
        $activeStatus = EventStatus::activeStatuses();

        return Event::whereIn('status', $activeStatus)->get();
    }

    public function getPublishedEventsForBrowse()
    {
        return Event::query()
            ->with(['hall'])
            ->whereIn('status', [
                EventStatus::UNDER_RESERVATIONS,
                EventStatus::READY_TO_START,
                EventStatus::OUT_OF_SEATS,
            ])
            ->orderBy('start_time')
            ->paginate(15);
    }

    public function getEventById($eventId)
    {
        return Event::with(['hall', 'seatPrices'])->find($eventId);
    }

    public function cancelEvent($event)
    {
        return $event->update([
            'status' => EventStatus::CANCELLED,
        ]);
    }

    public function getOverLappingEvent($startTime, $endTime, $hallId, $currentEventId = null)
    {
        $query = Event::query()
            ->where('hall_id', $hallId)
            ->where('status', '!=', EventStatus::CANCELLED)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($currentEventId) {
            $query->where('id', '!=', $currentEventId);
        }

        return $query->first();
    }

    public function occupyingReservationCount(Event $event): int
    {
        return $event->reservations()
            ->whereIn('status', ReservationStatus::occupying())
            ->count();
    }
}
