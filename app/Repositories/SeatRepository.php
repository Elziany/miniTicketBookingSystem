<?php

namespace App\Repositories;

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
                    ->whereIn('status', [
                        'held',
                        'pending_approval',
                        'confirmed',
                    ]);
            })
            ->whereHas('eventSeatPrices', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->orderBy('label')
            ->pluck('label', 'id');
    }

    public function lockSeatsForReservation($event, $seatIds)
    {
        $seats = Seat::query()
            ->where('hall_id', $event->hall_id)
            ->whereIn('id', $seatIds)
            ->lockForUpdate()
            ->get();

        if ($seats->count() !== count($seatIds)) {
            throw ValidationException::withMessages([
                'seat_ids' => 'One or more selected seats do not exist.',
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
                        ->whereIn('status', [
                            'held',
                            'pending_approval',
                            'confirmed',
                        ]);
                },
            ])
            ->orderBy('position_y')
            ->orderBy('position_x')
            ->get();
    }

}
