<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Str;

class OrderService
{
    public function __construct(private OrderRepository $orderRepository, private EventSeatPriceService $eventSeatPriceService, private ReservationService $reservationService, private SeatService $seatService) {}
    public function createOrder($userId, $eventId, $seatIds)
    {
        return DB::transaction(function () use (
            $userId,
            $eventId,
            $seatIds
        ) {
            $this->seatService->lockSeatsForReservation(
                $eventId,
                $seatIds
            );

            $totalAmount = $this->eventSeatPriceService
                ->getTotalSeatsPrice($eventId, $seatIds);

            $order = $this->orderRepository->createOrder([
                'user_id' => $userId,
                'status' => 'pending',
                'order_reference' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $totalAmount,
            ]);

            $this->reservationService->createReservations(
                $order->id,
                $eventId,
                $seatIds,
                $userId
            );

            return $order;
        });
    }
}
