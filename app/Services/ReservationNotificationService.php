<?php

namespace App\Services;

use App\Models\Reservation;

class ReservationNotificationService extends PushNotificationService
{
   
    public function notifyConfirmed(Reservation $reservation): void
    {
        $this->sendToUser(
            user: $reservation->user_id,
            title: 'Reservation Confirmed!',
            body: "Your reservation {$reservation->reference} for '{$reservation->event->name}' is confirmed.",
            data: [
                'type' => 'reservation_confirmed',
                'reservation_reference' => $reservation->reference,
                'event_id' => (string) $reservation->event_id,
            ],
            emailSubject: 'Your Reservation is Confirmed'
        );
    }

    public function notifyRejected(Reservation $reservation, string $reason = null): void
    {
        $this->sendToUser(
            user: $reservation->user_id,
            title: 'Reservation Rejected',
            body: "Your reservation {$reservation->reference} for '{$reservation->event->name}' was rejected. Reason: {$reason}",
            data: [
                'type' => 'reservation_rejected',
                'reservation_reference' => $reservation->reference,
                'reason' => $reason,
            ],
            emailSubject: 'Update on Your Reservation Request'
        );
    }


    public function notifyExpired(Reservation $reservation): void
    {
        $this->sendToUser(
            user: $reservation->user_id,
            title: 'Reservation Expired',
            body: "Your pending reservation {$reservation->reference} for '{$reservation->event->name}' has expired as it was not processed in time.",
            data: [
                'type' => 'reservation_expired',
                'reservation_reference' => $reservation->reference,
            ],
            emailSubject: 'Reservation Expired'
        );
    }
}