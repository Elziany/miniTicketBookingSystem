<?php

namespace App\Filament\Forms\Components;

use App\Enum\EventStatus;
use App\Models\Event;
use Filament\Forms\Components\Field;

class HallCalendarBoard extends Field
{
    protected string $view = 'filament.forms.components.hall-calendar-board';

    public function getEvents(): array
    {
        $hallId = $this->getLivewire()->getMountedTableActionRecord()?->id;

        if (! $hallId) {
            return [];
        }

        return Event::where('hall_id', $hallId)
            ->where('status', '!=', EventStatus::CANCELLED->value)
            ->get()
            ->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->start_time,
                'end' => $event->end_time,
                'backgroundColor' => '#4f46e5',
                'borderColor' => '#6366f1',
            ])
            ->toArray();
    }
}