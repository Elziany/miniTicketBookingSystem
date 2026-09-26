<?php

namespace App\Filament\Resources\EventSeatPrices\Pages;

use App\Filament\Resources\EventSeatPrices\EventSeatPriceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventSeatPrices extends ListRecords
{
    protected static string $resource = EventSeatPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
