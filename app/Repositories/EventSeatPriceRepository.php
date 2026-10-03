<?php

namespace App\Repositories;
use App\Models\EventSeatPrice;

class EventSeatPriceRepository
{

    public function getTotalPrice($eventId, $seatIds)
    {
        return EventSeatPrice::query()
            ->where('event_id', $eventId)
            ->whereIn('seat_id', $seatIds)
            ->sum('price');
    }

    public function getSeatPrice($eventId, $seatId)
    {
        return EventSeatPrice::query()
            ->where('event_id', $eventId)
            ->where('seat_id', $seatId)
            ->value('price');
    }

    public function countPricedSeats($eventId, $seatIds): int
    {
        return EventSeatPrice::query()
            ->where('event_id', $eventId)
            ->whereIn('seat_id', $seatIds)
            ->count();
    }
}
