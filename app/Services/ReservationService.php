<?php

namespace App\Services;

use App\Repositories\ReservationRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private EventSeatPriceService $eventSeatPriceService,
        private EventService $eventService,
        private QrcodeService $qrcodeService ,
        private ReservationNotificationService $reservationNotificationservice 
    ) {}

    public function createReservations(
        $orderId,
        $eventId,
        $seatIds,
        $userId
    ) {
        $event = $this->eventService->getEventById($eventId);

        foreach ($seatIds as $seatId) {

            $reservationData = $this->prepareReservationData($event, $orderId, $seatId, $userId);
            $reservation = $this->reservationRepository
                ->createReservation($reservationData);

            if ($reservation->status === 'confirmed') {
                $this->setReservationQrCode($reservation);
            }
        }
    }

    private function prepareReservationData($event, $orderId, $seatId, $userId)
    {
        $reservationStatus = $event->approval_mode === 'auto'
            ? 'confirmed'
            : 'pending_approval';

        $expiresAt = $event->approval_mode === 'manual'
            ? now()->addHours($event->approval_window_hours)
            : null;

        $reservationRef = 'RES-' . strtoupper(Str::random(10));

        return [
            'order_id' => $orderId,
            'event_id' => $event->id,
            'seat_id' => $seatId,
            'user_id' => $userId,

            'reservation_reference' => $reservationRef,

            'price_paid' => $this->eventSeatPriceService
                ->getSeatPrice($event->id, $seatId),

            'status' => $reservationStatus,

            'qr_code_path' => null,

            'expires_at' => $expiresAt,

            'refunded_amount' => 0,

            'cancellation_reason' => null,

            'rejection_reason' => null,
        ];
    }

    private function setReservationQrCode($reservation)
    {
        $qrPath = $this->qrcodeService
            ->generate($reservation->reservation_reference);

        $this->reservationRepository->updateReservation(
            $reservation,
            [
                'qr_code_path' => $qrPath,
            ]
        );
    }

    public function confirmReservation($reservation)
    {
        if ($reservation->status !== 'pending_approval') {
            throw ValidationException::withMessages([
                'reservation' => 'Only pending reservations can be approved.',
            ]);
        }
        $reservationData = [
            'status' => 'confirmed',
            'expires_at' => null
        ];
        $this->reservationRepository->updateReservation($reservation, $reservationData);
        $this->setReservationQrCode($reservation);
        $this->reservationNotificationservice->notifyConfirmed($reservation);
        return $reservation->refresh();
    }

    public function rejectReservation(
        $reservation,
        ?string $reason = null
    ) 
    {
        if ($reservation->status !== 'pending_approval') {
            throw ValidationException::withMessages([
                'reservation' => 'Only pending reservations can be rejected.',
            ]);
        }

        $this->reservationRepository->updateReservation(
            $reservation,
            [
                'status' => 'rejected',
                'expires_at' => null,
                'rejection_reason' => $reason,
            ]
        );
        $this->reservationNotificationservice->notifyRejected($reservation , $reason);
        return $reservation->refresh();
    }

    public function getReservationByReference($reservationRef){
      return  $this->reservationRepository->getReservationByReference($reservationRef);
    }
    public function getReservationsForReminder(
        string $timeframe,
        Carbon $targetTime
    ) {
        return $this->reservationRepository->getReservationsForReminder(
            $timeframe,
            $targetTime
        );
    }

    public function getReservationReadyForExpiration(){
        return $this->reservationRepository->getReservationReadyForExpiration();
    }
    public function getReservationsForFeedback(){
        return $this->reservationRepository->getReservationsForFeedback();
    }
    public function getReservationsByEvent($eventId){
        return $this->getReservationsByEvent($eventId);
    }
}
