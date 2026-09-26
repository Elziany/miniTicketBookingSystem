<?php

namespace App\Services;

use App\Repositories\EventSeatPriceRepository;

class EventSeatPriceService
{
    public function __construct(private EventSeatPriceRepository $eventSeatPriceRepository) {}
    public function getTotalSeatsPrice($eventId, $seatsIds)
    {
        return $this->eventSeatPriceRepository->getTotalPrice($eventId, $seatsIds);
    }

    public function getSeatPrice($eventId, $seatId)
    {
        return $this->eventSeatPriceRepository->getSeatPrice($eventId, $seatId);
    }
}
