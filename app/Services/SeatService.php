<?php 
namespace App\Services;
use App\Repositories\SeatRepository;

class SeatService {
    public function __construct(private SeatRepository $seatRepository , private EventService $eventService ) {
    }
    public function getAvailableSeatsByEvent($eventId) {

        $event = $this->eventService->getEventById($eventId);
        $availableSeats = $this->seatRepository->getAvailableSeats($event);
        return $availableSeats;
    }

    public function lockSeatsForReservation($eventId, $seatIds) {
        $event = $this->eventService->getEventById($eventId);
        return $this->seatRepository->lockSeatsForReservation($event, $seatIds);
    }

    public function getUnpricedSeatsByEvent($eventId) {
        $event = $this->eventService->getEventById($eventId);
        $availableSeats = $this->seatRepository->getUnpricedSeatsByEvent($event);
        return $availableSeats;
    }

    public function getSeatsForEventScreen($eventId){
        $event = $this->eventService->getEventById($eventId);
        return $this->seatRepository->getSeatsForEventScreen($event);
    }
}