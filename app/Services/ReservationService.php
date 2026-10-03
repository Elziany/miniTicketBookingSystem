<?php

namespace App\Services;

use App\Enum\ApprovalMode;
use App\Enum\EventStatus;
use App\Enum\OrderStatus;
use App\Enum\ReservationStatus;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\SystemConfiguration;
use App\Repositories\ReservationRepository;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private EventSeatPriceService $eventSeatPriceService,
        private EventService $eventService,
        private QrcodeService $qrcodeService,
        private ReservationNotificationService $reservationNotificationservice
    ) {}

    public function createReservations(
        $orderId,
        $eventId,
        $seatIds,
        $userId
    ) {
        $event = $this->eventService->getEventById($eventId);

        if (! $event) {
            throw ValidationException::withMessages([
                'event_id' => 'Event not found.',
            ]);
        }

        foreach ($seatIds as $seatId) {
            $reservationData = $this->prepareReservationData($event, $orderId, $seatId, $userId);
            $reservation = $this->reservationRepository->createReservation($reservationData);
            $this->applyApprovalMode($reservation, $event);
        }
    }

    public function applyApprovalMode(Reservation $reservation, Event $event): Reservation
    {
        $mode = $event->approval_mode instanceof ApprovalMode
            ? $event->approval_mode
            : ApprovalMode::from((string) $event->approval_mode);

        if ($mode === ApprovalMode::AUTO) {
            return $this->confirmHeldReservation($reservation);
        }

        $windowHours = max(1, (int) ($event->approval_window_hours ?: 24));

        return $this->reservationRepository->updateReservation($reservation, [
            'status' => ReservationStatus::PENDING_APPROVAL->value,
            'expires_at' => now()->addHours($windowHours),
            'qr_code_path' => null,
        ]);
    }

    private function prepareReservationData($event, $orderId, $seatId, $userId)
    {
        $price = $this->eventSeatPriceService->getSeatPrice($event->id, $seatId);

        if ($price === null) {
            throw ValidationException::withMessages([
                'seat_ids' => 'Every selected seat must have a price for this event.',
            ]);
        }

        $holdMinutes = SystemConfiguration::holdDurationMinutes();

        return [
            'order_id' => $orderId,
            'event_id' => $event->id,
            'seat_id' => $seatId,
            'user_id' => $userId,
            'reservation_reference' => 'RES-'.strtoupper(Str::random(10)),
            'price_paid' => $price,
            'status' => ReservationStatus::HELD->value,
            'qr_code_path' => null,
            'expires_at' => now()->addMinutes($holdMinutes),
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

    public function confirmHeldReservation(Reservation $reservation): Reservation
    {
        $this->reservationRepository->updateReservation($reservation, [
            'status' => ReservationStatus::CONFIRMED->value,
            'expires_at' => null,
        ]);
        $reservation->refresh();
        $this->setReservationQrCode($reservation);
        $reservation->loadMissing(['user', 'event']);
        $this->reservationNotificationservice->notifyConfirmed($reservation);

        return $reservation->refresh();
    }

    public function confirmReservation($reservation)
    {
        $status = $reservation->status instanceof ReservationStatus
            ? $reservation->status->value
            : $reservation->status;

        if ($status !== ReservationStatus::PENDING_APPROVAL->value) {
            throw ValidationException::withMessages([
                'reservation' => 'Only pending reservations can be approved.',
            ]);
        }

        return $this->confirmHeldReservation($reservation);
    }

    public function rejectReservation(
        $reservation,
        ?string $reason = null
    ) {
        $status = $reservation->status instanceof ReservationStatus
            ? $reservation->status->value
            : $reservation->status;

        if ($status !== ReservationStatus::PENDING_APPROVAL->value) {
            throw ValidationException::withMessages([
                'reservation' => 'Only pending reservations can be rejected.',
            ]);
        }

        $this->reservationRepository->updateReservation(
            $reservation,
            [
                'status' => ReservationStatus::REJECTED->value,
                'expires_at' => null,
                'rejection_reason' => $reason,
                'qr_code_path' => null,
            ]
        );
        $reservation->loadMissing(['user', 'event']);
        $this->reservationNotificationservice->notifyRejected($reservation, $reason);

        if ($reservation->order) {
            app(OrderService::class)->syncStatusFromReservations($reservation->order);
        }

        return $reservation->refresh();
    }

    public function expireReservation(Reservation $reservation, ReservationNotificationService $notificationService): Reservation
    {
        $this->reservationRepository->updateReservation($reservation, [
            'status' => ReservationStatus::EXPIRED->value,
            'expires_at' => null,
            'qr_code_path' => null,
        ]);
        $reservation->loadMissing(['user', 'event']);
        $notificationService->notifyExpired($reservation);

        if ($reservation->order) {
            app(OrderService::class)->syncStatusFromReservations($reservation->order);
        }

        return $reservation->refresh();
    }

    public function cancelReservation(Reservation $reservation, ?string $reason = null, string $refundedAmount = '0.00'): Reservation
    {
        $this->reservationRepository->updateReservation($reservation, [
            'status' => ReservationStatus::CANCELLED->value,
            'expires_at' => null,
            'qr_code_path' => null,
            'cancellation_reason' => $reason,
            'refunded_amount' => $refundedAmount,
        ]);

        return $reservation->refresh();
    }

    public function getReservationByReference($reservationRef)
    {
        return $this->reservationRepository->getReservationByReference($reservationRef);
    }

    public function getReservationsForReminder(
        string $timeframe,
        \Carbon\Carbon $targetTime
    ) {
        return $this->reservationRepository->getReservationsForReminder(
            $timeframe,
            $targetTime
        );
    }

    public function getHeldReservationsReadyForExpiration()
    {
        return $this->reservationRepository->getHeldReservationsReadyForExpiration();
    }

    public function getReservationReadyForExpiration()
    {
        return $this->reservationRepository->getReservationReadyForExpiration();
    }

    public function getReservationsForFeedback()
    {
        return $this->reservationRepository->getReservationsForFeedback();
    }

    public function getReservationsByEvent($eventId)
    {
        return $this->reservationRepository->getReservationsByEvent($eventId);
    }
}
