<?php

namespace App\Enum;

enum EventStatus: string
{
    case UNPUBLISHED = 'unpublished';
    case UNDER_RESERVATIONS = 'under_reservations';
    case OUT_OF_SEATS = 'out_of_seats';
    case READY_TO_START = 'ready_to_start';
    case STARTED = 'started';
    case ENDED = 'ended';
    case CANCELLED = 'cancelled';

    public static function options(): array
    {
        return [
            self::UNPUBLISHED->value => 'Unpublished',
            self::UNDER_RESERVATIONS->value => 'Under reservations',
            self::OUT_OF_SEATS->value => 'Out of seats',
            self::READY_TO_START->value => 'Ready to start',
            self::STARTED->value => 'Started',
            self::ENDED->value => 'Ended',
            self::CANCELLED->value => 'Cancelled',
        ];
    }

    public static function activeStatuses(): array
    {
        return [
            self::UNDER_RESERVATIONS->value,
            self::READY_TO_START->value,
            self::STARTED->value,
        ];
    }

}