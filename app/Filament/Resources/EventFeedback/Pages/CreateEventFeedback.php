<?php

namespace App\Filament\Resources\EventFeedback\Pages;

use App\Filament\Resources\EventFeedback\EventFeedbackResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventFeedback extends CreateRecord
{
    protected static string $resource = EventFeedbackResource::class;
}
