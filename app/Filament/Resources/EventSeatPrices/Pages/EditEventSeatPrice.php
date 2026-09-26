<?php

namespace App\Filament\Resources\EventSeatPrices\Pages;

use App\Filament\Resources\EventSeatPrices\EventSeatPriceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventSeatPrice extends EditRecord
{
    protected static string $resource = EventSeatPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
