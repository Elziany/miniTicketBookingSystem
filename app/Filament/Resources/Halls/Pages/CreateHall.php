<?php

namespace App\Filament\Resources\Halls\Pages;

use App\Filament\Resources\Halls\HallResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHall extends CreateRecord
{
    protected static string $resource = HallResource::class;
    protected function afterCreate(): void
    {
        $this->record->syncSeatGrid($this->data['seat_labels'] ?? []);
    }

}
