<?php

namespace App\Services;

use App\Enum\CheckInType;
use App\Enum\EventStatus;
use App\Repositories\AttendanceRepository;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(
        private ReservationService $reservationService,
        private AttendanceRepository $attendanceRepository
    ) {}

    public function checkInByQr(
        string $reservationRef,
        int $agentId
    ) {
        $reservation = $this->validateReservationForCheckIn($reservationRef);
        return $this->attendanceRepository->createAttendance([
            'reservation_id' => $reservation->id,
            'checked_in_by' => $agentId,
            'check_in_type' => CheckInType::QR,
            'manual_check_in_note' => null,
            'checked_in_at' => now(),
        ]);
    }

    public function checkInManually(
        string $reservationRef,
        int $agentId,
        string $note
    ) {
        $reservation = $this->validateReservationForCheckIn($reservationRef);

        if (empty(trim($note))) {
            throw ValidationException::withMessages([
                'manual_check_in_note' => 'A reason is required for manual check-in.',
            ]);
        }

        return $this->attendanceRepository->createAttendance([
            'reservation_id' => $reservation->id,
            'checked_in_by' => $agentId,
            'check_in_type' => 'manual',
            'manual_check_in_note' => $note,
            'checked_in_at' => now(),
        ]);
    }

    private function validateReservationForCheckIn(string $reservationRef)
    {
        $reservation = $this->reservationService
            ->getReservationByReference($reservationRef);

        if (! $reservation) {
            throw ValidationException::withMessages([
                'reservation_reference' => 'Invalid reservation.',
            ]);
        }

        $status = $reservation->status instanceof \App\Enum\ReservationStatus
            ? $reservation->status
            : \App\Enum\ReservationStatus::from((string) $reservation->status);

        if ($status !== \App\Enum\ReservationStatus::CONFIRMED) {
            throw ValidationException::withMessages([
                'reservation_reference' => 'This reservation is not valid for check-in.',
            ]);
        }

        if (! $reservation->event) {
            throw ValidationException::withMessages([
                'reservation_reference' => 'The reservation event could not be found.',
            ]);
        }


        $eventStatus = $reservation->event->status instanceof EventStatus
            ? $reservation->event->status
            : EventStatus::from((string) $reservation->event->status);

        if (! in_array($eventStatus, [
            EventStatus::STARTED,
            EventStatus::READY_TO_START,
        ], true)) {
            throw ValidationException::withMessages([
                'reservation_reference' => 'Check-in is not available for this event.',
            ]);
        }

        $alreadyExists = $this->attendanceRepository
            ->getAttendanceByReservation($reservation->id);

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'reservation_reference' => 'Guest is already checked in.',
            ]);
        }

        return $reservation;
    }
}
