<?php
namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class QrcodeService {
 public function generate($reservationRef): string
    {

       $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $reservationRef,
            size: 300,
            margin: 10,
        );

        $path = 'reservations/qr/' .
            $reservationRef . '.png';

       Storage::disk('public')->put(
            $path,
            $builder->build()->getString()
        );

        return $path;
    }
}