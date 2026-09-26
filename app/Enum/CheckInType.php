<?php
namespace App\Enum;
enum CheckInType: string
{
    case QR = 'qr_scan';
    case MANUAL = 'manual';
}