<?php

namespace App\Repositories;

use App\Enum\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationRepository
{
    public function createReservation($reservationData)
    {
        return Reservation::create($reservationData);
    }

    public function updateReservation($reservation, $reservationData)
    {
        $reservation->update($reservationData);

        return $reservation;
    }

    public function getReservationByReference($reservationRef)
    {
        return Reservation::where('reservation_reference', $reservationRef)
            ->with(['event', 'attendance', 'order'])
            ->first();
    }

    public function getReservationsForReminder(
        string $timeframe,
        Carbon $targetTime
    ) {
        return Reservation::query()
            ->where('status', ReservationStatus::CONFIRMED)
            ->whereDoesntHave('reminders', function ($query) use ($timeframe) {
                $query->where('timeframe', $timeframe);
            })
            ->whereHas('event', function ($query) use ($targetTime) {
                $query->whereBetween('start_time', [
                    $targetTime,
                    $targetTime->copy()->addMinute(),
                ])
                    ->whereNotIn('status', ['cancelled', 'ended']);
            })
            ->with(['event', 'user'])
            ->get();
    }

    public function getHeldReservationsReadyForExpiration()
    {
        return Reservation::query()
            ->where('status', ReservationStatus::HELD)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with(['event', 'user', 'order'])
            ->get();
    }

    public function getReservationReadyForExpiration()
    {
        return Reservation::query()
            ->where('status', ReservationStatus::PENDING_APPROVAL)
            ->where(function ($query) {
                $query->where(function ($inner) {
                    $inner->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now());
                })->orWhereHas('event', function ($eventQuery) {
                    $eventQuery->whereIn('status', ['ready_to_start', 'started', 'ended', 'cancelled']);
                });
            })
            ->with(['event', 'user', 'order'])
            ->get();
    }

    public function getReservationsForFeedback()
    {
        $now = Carbon::now();

        return Reservation::query()
            ->where('status', ReservationStatus::CONFIRMED)
            ->whereHas('attendance')
            ->whereNull('feedback_requested_at')
            ->whereHas('event', function ($query) use ($now) {
                $query->where('end_time', '<=', $now)
                    ->whereNotIn('status', ['cancelled']);
            })
            ->with(['event', 'user', 'order'])
            ->get();
    }

    public function getReservationsByEvent($eventId)
    {
        return Reservation::query()
            ->where('event_id', $eventId)
            ->whereIn('status', ReservationStatus::occupying())
            ->with(['user', 'event']);
    }
}
