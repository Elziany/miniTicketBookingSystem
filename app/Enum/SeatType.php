<?php

namespace App\Enum;

use function CuyZ\Valinor\Compiler\return_;

enum SeatType: string
{
    case STANDARD = 'standard';
    case VIP = 'vip';

    public static function options(): array
    {
        return  [
            self::STANDARD->value => 'Standard',
            self::VIP->value => 'VIP',
        ];
    }
}
