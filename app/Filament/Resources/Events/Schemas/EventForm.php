<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enum\EventStatus;
use App\Models\Event;
use App\Services\EventService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('hall_id')
                ->relationship('hall', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->live(),

            ViewField::make('calendar_board')
                ->hiddenLabel()
                ->view('filament.forms.components.hall-calendar-board')
                ->dehydrated(false)
                ->columnSpanFull()
                ->viewData(fn ($get, $record) => [
                    'hallId' => $get('hall_id'),
                    'events' => self::calendarEvents($get('hall_id'), $record?->id),
                ]),

            TextInput::make('name')
                ->required(),

            Textarea::make('description')
                ->columnSpanFull(),

            DateTimePicker::make('start_time')
                ->required(),

            DateTimePicker::make('end_time')
                ->required()
                ->after('start_time')
                ->rule(fn ($get, $record) => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                    $start = $get('start_time');
                    $hallId = $get('hall_id');

                    if (! $start || ! $value || ! $hallId) {
                        return;
                    }

                    $overlap = app(EventService::class)
                        ->getOverLappingEvent($start, $value, $hallId, $record?->id);

                    if ($overlap) {
                        $fail(
                            "The selected time overlaps with the event \"{$overlap->name}\" "
                            . "({$overlap->start_time} - {$overlap->end_time})."
                        );
                    }
                }),

            Select::make('status')
                ->options(EventStatus::options())
                ->default(EventStatus::UNDER_RESERVATIONS->value)
                ->required(),

            Select::make('approval_mode')
                ->options([
                    'auto' => 'Auto',
                    'manual' => 'Manual',
                ])
                ->default('auto')
                ->required(),

            TextInput::make('approval_window_hours')
                ->numeric(),

            Select::make('pricing_mode')
                ->options([
                    'per_seat' => 'Per seat',
                    'per_category' => 'Per category',
                ])
                ->required(),

            TextInput::make('custom_refund_policy'),

            Textarea::make('cancellation_reason')
                ->columnSpanFull(),
        ]);
    }

    private static function calendarEvents(?int $hallId, ?int $ignoreEventId = null): array
    {
        if (! $hallId) {
            return [];
        }

        return Event::where('hall_id', $hallId)
            ->where('status', '!=', EventStatus::CANCELLED)
            ->when($ignoreEventId, fn ($query) => $query->whereKeyNot($ignoreEventId))
            ->get()
            ->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->start_time,
                'end' => $event->end_time,
                'backgroundColor' => '#4f46e5',
                'borderColor' => '#6366f1',
            ])
            ->all();
    }
}