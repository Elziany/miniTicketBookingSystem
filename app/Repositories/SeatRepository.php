<?php

namespace App\Repositories;

use App\Enum\ReservationStatus;
use App\Models\Reservation;
use App\Models\Seat;
use Illuminate\Validation\ValidationException;

class SeatRepository
{
    public function getAvailableSeats($event)
    {
        return Seat::query()
            ->where('hall_id', $event->hall_id)
            ->whereDoesntHave('reservations', function ($query) use ($event) {
                $query->where('event_id', $event->id)
                    ->whereIn('status', ReservationStatus::occupying());
            })
            ->whereHas('eventSeatPrices', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->orderBy('label')
            ->pluck('label', 'id');
    }

    public function countAvailableSeats($event): int
    {
        return Seat::query()
            ->where('hall_id', $event->hall_id)
            ->whereDoesntHave('reservations', function ($query) use ($event) {
                $query->where('event_id', $event->id)
                    ->whereIn('status', ReservationStatus::occupying());
            })
            ->whereHas('eventSeatPrices', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->count();
    }

    public function lockSeatsForReservation($event, $seatIds)
    {
        $seats = Seat::query()
            ->where('hall_id', $event->hall_id)
            ->whereIn('id', $seatIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($seats->count() !== count($seatIds)) {
            throw ValidationException::withMessages([
                'seat_ids' => 'One or more selected seats do not exist in this hall.',
            ]);
        }

        Reservation::query()
            ->where('event_id', $event->id)
            ->whereIn('seat_id', $seatIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $occupied = Reservation::query()
            ->where('event_id', $event->id)
            ->whereIn('seat_id', $seatIds)
            ->whereIn('status', ReservationStatus::occupying())
            ->exists();

        if ($occupied) {
            throw ValidationException::withMessages([
                'seat_ids' => 'One or more selected seats are no longer available. No seats were booked.',
            ]);
        }

        return $seats;
    }

    public function getUnpricedSeatsByEvent($event)
    {
        return Seat::query()
            ->where('hall_id', $event->hall_id)
            ->whereDoesntHave('eventSeatPrices', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->orderBy('label')
            ->pluck('label', 'id')
            ->toArray();
    }

    public function getSeatsForEventScreen($event)
    {
        return Seat::query()
            ->where('hall_id', $event->hall_id)
            ->whereHas('eventSeatPrices', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->with([
                'eventSeatPrices' => function ($query) use ($event) {
                    $query->where('event_id', $event->id);
                },
                'reservations' => function ($query) use ($event) {
                    $query
                        ->where('event_id', $event->id)
                        ->whereIn('status', ReservationStatus::occupying());
                },
            ])
            ->orderBy('position_y')
            ->orderBy('position_x')
            ->get();
    }
}
