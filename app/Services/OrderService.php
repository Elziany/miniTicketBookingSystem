<?php

namespace App\Services;

use App\Enum\EventStatus;
use App\Enum\OrderStatus;
use App\Enum\ReservationStatus;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EventSeatPriceService $eventSeatPriceService,
        private ReservationService $reservationService,
        private SeatService $seatService,
        private EventService $eventService
    ) {}

    public function createOrder($userId, $eventId, $seatIds)
    {
        $seatIds = array_values(array_unique(array_map('intval', (array) $seatIds)));

        if ($seatIds === []) {
            throw ValidationException::withMessages([
                'seat_ids' => 'Select at least one seat.',
            ]);
        }

        return DB::transaction(function () use ($userId, $eventId, $seatIds) {
            $event = $this->eventService->getEventById($eventId);

            if (! $event) {
                throw ValidationException::withMessages([
                    'event_id' => 'Event not found.',
                ]);
            }

            $status = $event->status instanceof EventStatus
                ? $event->status
                : EventStatus::from((string) $event->status);

            if (! $status->isBookable()) {
                throw ValidationException::withMessages([
                    'event_id' => 'This event is not open for reservations.',
                ]);
            }

            $this->seatService->lockSeatsForReservation($eventId, $seatIds);

            $pricedCount = $this->eventSeatPriceService->countPricedSeats($eventId, $seatIds);
            if ($pricedCount !== count($seatIds)) {
                throw ValidationException::withMessages([
                    'seat_ids' => 'Every selected seat must have a price for this event.',
                ]);
            }

            $totalAmount = $this->eventSeatPriceService->getTotalSeatsPrice($eventId, $seatIds);

            $order = $this->orderRepository->createOrder([
                'user_id' => $userId,
                'status' => OrderStatus::PENDING->value,
                'order_reference' => 'ORD-'.strtoupper(Str::random(10)),
                'total_amount' => $totalAmount,
            ]);

            $this->reservationService->createReservations(
                $order->id,
                $eventId,
                $seatIds,
                $userId
            );

            $order->load('reservations');
            $this->syncStatusFromReservations($order);
            $this->eventService->refreshOccupancyStatus($event->fresh());

            return $order->fresh(['reservations']);
        });
    }

    public function syncStatusFromReservations(Order $order): Order
    {
        $order->load('reservations');
        $statuses = $order->reservations
            ->map(fn ($reservation) => $reservation->status instanceof ReservationStatus
                ? $reservation->status->value
                : $reservation->status)
            ->unique()
            ->values();

        $derived = OrderStatus::PENDING->value;

        if ($statuses->every(fn ($status) => $status === ReservationStatus::CONFIRMED->value)) {
            $derived = OrderStatus::CONFIRMED->value;
        } elseif ($statuses->every(fn ($status) => $status === ReservationStatus::CANCELLED->value || $status === ReservationStatus::EXPIRED->value)) {
            $derived = OrderStatus::CANCELLED->value;
        } elseif ($statuses->every(fn ($status) => $status === ReservationStatus::REJECTED->value)) {
            $derived = OrderStatus::REJECTED->value;
        } elseif ($statuses->contains(ReservationStatus::CONFIRMED->value)
            && $statuses->intersect([
                ReservationStatus::CANCELLED->value,
                ReservationStatus::EXPIRED->value,
                ReservationStatus::REJECTED->value,
            ])->isNotEmpty()) {
            $derived = OrderStatus::PARTIALLY_CANCELLED->value;
        }

        $order->update(['status' => $derived]);

        return $order->refresh();
    }

    public function cancelOwnedOrder(Order $order, int $userId, ?string $reason = null): Order
    {
        if ((int) $order->user_id !== $userId) {
            abort(403, 'You cannot cancel another user\'s order.');
        }

        return DB::transaction(function () use ($order, $reason) {
            $order->load('reservations.event');

            foreach ($order->reservations as $reservation) {
                $status = $reservation->status instanceof ReservationStatus
                    ? $reservation->status->value
                    : $reservation->status;

                if (! in_array($status, ReservationStatus::occupying(), true)) {
                    continue;
                }

                $this->reservationService->cancelReservation($reservation, $reason, '0.00');
            }

            $this->syncStatusFromReservations($order->fresh('reservations'));

            $event = $order->reservations->first()?->event;
            if ($event) {
                $this->eventService->refreshOccupancyStatus($event);
            }

            return $order->fresh(['reservations']);
        });
    }
}
