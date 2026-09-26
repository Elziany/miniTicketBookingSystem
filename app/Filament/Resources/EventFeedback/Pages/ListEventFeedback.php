<?php

namespace App\Filament\Resources\EventFeedback\Pages;

use App\Filament\Resources\EventFeedback\EventFeedbackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventFeedback extends ListRecords
{
    protected static string $resource = EventFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
