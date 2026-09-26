<?php

namespace App\Filament\Resources\Events\Pages;

use App\Events\EventRescheduled;
use App\Filament\Resources\Events\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        if ($this->record->wasChanged(['start_time', 'end_time'])) {
            EventRescheduled::dispatch($this->record);
        }
    }
}
