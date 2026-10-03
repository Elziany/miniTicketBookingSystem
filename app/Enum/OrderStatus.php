<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PARTIALLY_CANCELLED = 'partially_cancelled';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';
}
