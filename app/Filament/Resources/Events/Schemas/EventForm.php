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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('availability')
                    ->description('Pick a hall to see its existing events before choosing a time.')
                    ->icon(Heroicon::BuildingOffice2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('hall_id')
                            ->label('Hall')
                            ->relationship('hall', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->native(false)
                            ->placeholder('Select a hall'),

                        ViewField::make('calendar_board')
                            ->hiddenLabel()
                            ->view('filament.forms.components.hall-calendar-board')
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->viewData(fn ($get, $record) => [
                                'hallId' => $get('hall_id'),
                                'events' => self::calendarEvents($get('hall_id'), $record?->id),
                            ]),
                    ]),

                Section::make('Event details')
                    ->description('What is this event about?')
                    ->icon(Heroicon::DocumentText)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Event name')
                            ->placeholder('e.g. Summer Concert')
                            ->prefixIcon(Heroicon::Tag)
                            ->required(),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Tell attendees what to expect...')
                            ->rows(4),
                    ]),

                // ───────────── Schedule ─────────────
                Section::make('Schedule')
                    ->description('Events in the same hall cannot overlap.')
                    ->icon(Heroicon::CalendarDays)
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        DateTimePicker::make('start_time')
                            ->label('Starts at')
                            ->prefixIcon(Heroicon::Clock)
                            ->native(false)
                            ->seconds(false)
                            ->required(),

                        DateTimePicker::make('end_time')
                            ->label('Ends at')
                            ->prefixIcon(Heroicon::Clock)
                            ->native(false)
                            ->seconds(false)
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
                    ]),

                // ───────────── Status & approval ─────────────
                Section::make('Status & approval')
                    ->description('Control the event status and how reservations are approved.')
                    ->icon(Heroicon::CheckBadge)
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(EventStatus::options())
                            ->default(EventStatus::UNDER_RESERVATIONS->value)
                            ->native(false)
                            ->required(),

                        Select::make('approval_mode')
                            ->label('Approval mode')
                            ->options([
                                'auto' => 'Auto',
                                'manual' => 'Manual',
                            ])
                            ->default('auto')
                            ->native(false)
                            ->helperText('Auto confirms reservations instantly.')
                            ->required(),

                        TextInput::make('approval_window_hours')
                            ->label('Approval window')
                            ->numeric()
                            ->suffix('hours')
                            ->placeholder('e.g. 24')
                            ->helperText('Time allowed to approve a reservation.'),
                    ]),

                // ───────────── Pricing & refunds ─────────────
                Section::make('Pricing & refunds')
                    ->description('How tickets are priced and what happens on refunds.')
                    ->icon(Heroicon::Banknotes)
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('pricing_mode')
                            ->label('Pricing mode')
                            ->options([
                                'per_seat' => 'Per seat',
                                'per_category' => 'Per category',
                            ])
                            ->native(false)
                            ->placeholder('Select pricing mode')
                            ->required(),

                        TextInput::make('custom_refund_policy')
                            ->label('Custom refund policy')
                            ->placeholder('Optional, e.g. 50% refund up to 48h before')
                            ->prefixIcon(Heroicon::ReceiptRefund),
                    ]),

                // ───────────── Cancellation ─────────────
                Section::make('Cancellation')
                    ->description('Fill this in only if the event is cancelled.')
                    ->icon(Heroicon::XCircle)
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('cancellation_reason')
                            ->label('Cancellation reason')
                            ->placeholder('Why was this event cancelled?')
                            ->rows(3),
                    ]),
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