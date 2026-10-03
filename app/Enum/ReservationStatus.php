<?php

namespace App\Enum;

enum ReservationStatus: string
{
    case HELD = 'held';
    case PENDING_APPROVAL = 'pending_approval';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    /**
     * Statuses that occupy a seat for an event.
     *
     * @return list<string>
     */
    public static function occupying(): array
    {
        return [
            self::HELD->value,
            self::PENDING_APPROVAL->value,
            self::CONFIRMED->value,
        ];
    }
}
