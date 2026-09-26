<?php
namespace App\Repositories;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationRepository {
    public function createReservation($reservationData) {
        return Reservation::create($reservationData);
    }

    public function updateReservation($reservation , $reservationData){
        $reservation->update($reservationData);
        return $reservation;
    }

    public function getReservationByReference($reservationRef){
        return Reservation::where('reservation_reference' , $reservationRef)->
        with('event')->first();
    }

    public function getReservationsForReminder(
        string $timeframe,
        Carbon $targetTime
    ) {


        return Reservation::query()
            ->where('status', 'confirmed')
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

    public function getReservationReadyForExpiration()
    {
        $now = Carbon::now();
        return Reservation::where('status', 'pending_approval')
            ->whereHas('event', function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereRaw("created_at + INTERVAL approval_window_hours HOUR <= ?", [$now]);
                })->orWhereIn('status', ['ready_to_start', 'started', 'ended', 'cancelled']);
            })
            ->with(['event', 'user'])
            ->get();
    }

    public function getReservationsForFeedback()
    {
        $now = Carbon::now();
        return Reservation::whereHas('attendance')
            ->whereNull('feedback_requested_at')
            ->whereHas('event', function ($query) use ($now) {
                $query->where('end_time', '<=', $now)
                    ->whereNotIn('status', ['cancelled']);
            })
            ->with(['event', 'user'])
            ->get();
    }

    public function getReservationsByEvent($eventId)
    {
        return Reservation::whereIn('status', ['confirmed', 'pending_approval'])
            ->with(['user']);
    }
}