<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Reservation;

class EventNotificationService extends PushNotificationService
{
    public function notifyCancelled(Reservation $reservation, string $reason = null): void
    {
        $this->sendToUser(
            user: $reservation->user_id,
            title: 'Event Cancelled',
            body: "The event '{$reservation->event->name}' has been cancelled. Reason: {$reason}. A refund has been issued.",
            data: [
                'type' => 'event_cancelled',
                'event_id' => (string) $reservation->event_id,
                'reason' => $reason,
            ],
            emailSubject: 'Important: Event Cancellation Notice'
        );
    }

    public function notifyReminder(Reservation $reservation, string $timeframe): void
    {
        $timeframeText = match ($timeframe) {
            '1_day' => 'tomorrow',
            '1_hour' => 'in 1 hour',
            '10_minutes' => 'in 10 minutes',
            default => 'soon',
        };

        $this->sendToUser(
            user: $reservation->user_id,
            title: 'Upcoming Event Reminder',
            body: "Reminder: Your event '{$reservation->event->name}' starts {$timeframeText}!",
            data: [
                'type' => 'event_reminder',
                'event_id' => (string) $reservation->event_id,
                'timeframe' => $timeframe,
            ],
            emailSubject: "Reminder: {$reservation->event->name} starts {$timeframeText}"
        );
    }

    public function notifyFeedbackRequest(Reservation $reservation): void
    {
        $this->sendToUser(
            user: $reservation->user_id,
            title: 'How was your experience?',
            body: "Thank you for attending '{$reservation->event->name}'! Please take a moment to leave us your feedback.",
            data: [
                'type' => 'feedback_request',
                'event_id' => (string) $reservation->event_id,
                'order_reference' => $reservation->order?->reference ?? '',
            ],
            emailSubject: "Rate your experience at {$reservation->event->name}"
        );
    }

    public function notifyRescheduled(Reservation $reservation, Event $event): void
    {
        $formattedTime = $event->start_time->format('M d, Y H:i');

        $this->sendToUser(
            user: $reservation->user_id,
            title: 'Event Schedule Updated',
            body: "The event '{$event->name}' has been rescheduled to {$formattedTime}.",
            data: [
                'type' => 'event_rescheduled',
                'event_id' => (string) $event->id,
                'new_start_time' => $event->start_time->toIso8601String(),
            ],
            emailSubject: "Schedule Update: {$event->name}"
        );
    }
}