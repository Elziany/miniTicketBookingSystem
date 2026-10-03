<?php

namespace App\Services;

use App\Enum\EventStatus;
use App\Enum\ReservationStatus;
use App\Events\CancellEvent;
use App\Events\EventRescheduled;
use App\Models\Event;
use App\Repositories\EventRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventService
{
    public function __construct(
        private EventRepository $eventRepository,
        private EventNotificationService $eventNotificationService,
        private RefundCalculator $refundCalculator
    ) {}

    public function getActiveEvents(): Collection
    {
        return $this->eventRepository->getActiveEvents();
    }

    public function getPublishedEventsForBrowse()
    {
        return $this->eventRepository->getPublishedEventsForBrowse();
    }

    public function getEventById($eventId)
    {
        return $this->eventRepository->getEventById($eventId);
    }

    public function transition(Event $event, EventStatus $target): Event
    {
        $current = $event->status instanceof EventStatus
            ? $event->status
            : EventStatus::from((string) $event->status);

        if ($current === $target) {
            return $event;
        }

        if (! $current->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "Cannot change event from {$current->value} to {$target->value}.",
            ]);
        }

        $payload = ['status' => $target];

        if ($target === EventStatus::STARTED) {
            $payload['started_at'] = now();
        }

        if ($target === EventStatus::ENDED) {
            $payload['ended_at'] = now();
        }

        $event->update($payload);

        return $event->refresh();
    }

    public function publish(Event $event): Event
    {
        return $this->transition($event, EventStatus::UNDER_RESERVATIONS);
    }

    public function markReadyToStart(Event $event): Event
    {
        return $this->transition($event, EventStatus::READY_TO_START);
    }

    public function start(Event $event): Event
    {
        return $this->transition($event, EventStatus::STARTED);
    }

    public function end(Event $event): Event
    {
        return $this->transition($event, EventStatus::ENDED);
    }

    public function cancelEvent($event, ?string $reason = null)
    {
        $event = $event instanceof Event ? $event : $this->getEventById($event);

        $current = $event->status instanceof EventStatus
            ? $event->status
            : EventStatus::from((string) $event->status);

        if ($current === EventStatus::CANCELLED) {
            throw ValidationException::withMessages([
                'status' => 'This event is already cancelled.',
            ]);
        }

        if ($current->isTerminal() && $current !== EventStatus::CANCELLED) {
            throw ValidationException::withMessages([
                'status' => 'An ended event cannot be cancelled.',
            ]);
        }

        return DB::transaction(function () use ($event, $reason) {
            $event->update([
                'status' => EventStatus::CANCELLED,
                'cancellation_reason' => $reason,
            ]);

            $event->load(['reservations.user', 'reservations.event']);

            $reservationService = app(ReservationService::class);

            foreach ($event->reservations as $reservation) {
                $status = $reservation->status instanceof ReservationStatus
                    ? $reservation->status->value
                    : $reservation->status;

                if (! in_array($status, ReservationStatus::occupying(), true)) {
                    continue;
                }

                $refund = $this->refundCalculator->amount((float) $reservation->price_paid, $event);
                $reservationService->cancelReservation(
                    $reservation,
                    $reason ?? $event->cancellation_reason,
                    $refund
                );
            }

            $orderService = app(OrderService::class);
            $event->reservations->pluck('order_id')->unique()->each(function ($orderId) use ($orderService) {
                $order = \App\Models\Order::query()->find($orderId);
                if ($order) {
                    $orderService->syncStatusFromReservations($order);
                }
            });

            CancellEvent::dispatch($event->fresh());

            return $event->fresh();
        });
    }

    public function refreshOccupancyStatus(Event $event): Event
    {
        $status = $event->status instanceof EventStatus
            ? $event->status
            : EventStatus::from((string) $event->status);

        if (! in_array($status, [EventStatus::UNDER_RESERVATIONS, EventStatus::OUT_OF_SEATS, EventStatus::READY_TO_START], true)) {
            return $event;
        }

        $available = app(SeatService::class)->countAvailableSeats($event);

        if ($available === 0 && $status === EventStatus::UNDER_RESERVATIONS) {
            return $this->transition($event, EventStatus::OUT_OF_SEATS);
        }

        if ($available > 0 && $status === EventStatus::OUT_OF_SEATS) {
            return $this->transition($event, EventStatus::UNDER_RESERVATIONS);
        }

        return $event;
    }

    public function reschedule(Event $event, $startTime, $endTime): Event
    {
        $overlapping = $this->getOverLappingEvent($startTime, $endTime, $event->hall_id, $event->id);

        if ($overlapping) {
            throw ValidationException::withMessages([
                'start_time' => 'The selected time overlaps with another event in this hall.',
            ]);
        }

        $event->update([
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        EventRescheduled::dispatch($event->fresh());

        return $event->fresh();
    }

    public function getOverLappingEvent($startTime, $endTime, $hallId, $currentEventId = null)
    {
        return $this->eventRepository->getOverLappingEvent($startTime, $endTime, $hallId, $currentEventId);
    }
}
