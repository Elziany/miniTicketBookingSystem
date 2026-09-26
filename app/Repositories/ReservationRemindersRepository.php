<?php
namespace App\Repositories;

use App\Models\ReservationReminders;

class ReservationRemindersRepository {
    public function resetRemindersForEvent(int $eventId): int
    {
        return ReservationReminders::whereHas('reservation', function ($query) use ($eventId) {
            $query->where('event_id', $eventId);
        })->delete();
    }
}
