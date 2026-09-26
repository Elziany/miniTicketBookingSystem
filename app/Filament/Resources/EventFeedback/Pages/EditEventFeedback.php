<?php

namespace App\Filament\Resources\EventFeedback\Pages;

use App\Filament\Resources\EventFeedback\EventFeedbackResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventFeedback extends EditRecord
{
    protected static string $resource = EventFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
