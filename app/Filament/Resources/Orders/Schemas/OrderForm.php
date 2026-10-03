<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Event;
use App\Services\EventSeatPriceService;
use App\Services\EventService;
use App\Services\SeatService;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order')->schema([
                Select::make('user_id')
                    ->label('Customer')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('event_id')
                    ->label('Event')
                    ->options(fn () => app(EventService::class)->getActiveEvents()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('seat_ids', [])) // new event = new seat map
                    ->required(),

                ViewField::make('seat_ids')
                    ->label('Seats')
                    ->view('filament.forms.components.seat-picker')
                    ->default([])
                    ->live()
                    ->columnSpanFull()
                    ->viewData(fn ($get) => self::seatMap($get('event_id')))
                    ->required()
                    ->rules([
                        'array',
                        'min:1',
                        // Re-check availability on submit in case someone else booked meanwhile.
                        fn ($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $available = self::availableSeatIds($get('event_id'));

                            if (array_diff(array_map('intval', (array) $value), $available)) {
                                $fail('One or more selected seats are no longer available.');
                            }
                        },
                    ]),

                Placeholder::make('reservation_count')
                    ->label('Number of Reservations')
                    ->content(fn ($get) => count($get('seat_ids') ?? [])),

                Placeholder::make('total_amount')
                    ->label('Total Amount')
                    ->content(function ($get) {
                        $eventId = $get('event_id');
                        $seatIds = $get('seat_ids') ?? [];

                        if (! $eventId || empty($seatIds)) {
                            return '0.00 EGP';
                        }

                        $total = app(EventSeatPriceService::class)->getTotalSeatsPrice($eventId, $seatIds);

                        return number_format($total, 2) . ' EGP';
                    }),
            ]),
        ]);
    }

    /** IDs of seats that can still be booked for this event. */
    private static function availableSeatIds(?int $eventId): array
    {
        if (! $eventId) {
            return [];
        }

        // getAvailableSeatsByEvent() returns [seat_id => label], as the old Select used it.
        return collect(app(SeatService::class)->getAvailableSeatsByEvent($eventId))
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /** Data for the cinema-style seat picker view. */
    private static function seatMap(?int $eventId): array
    {
        $empty = ['rows' => 0, 'cols' => 0, 'seats' => []];

        if (! $eventId || ! ($event = Event::with('hall.seats')->find($eventId)) || ! $event->hall) {
            return $empty;
        }

        $available = self::availableSeatIds($eventId);

        $hallCols = max(1, (int) $event->hall->column_count);

        $seats = $event->hall->seats
            ->sortBy('id')
            ->values()
            ->map(fn ($seat, $i) => [
                'id' => $seat->id,
                'label' => $seat->label,
                'x' => $seat->position_x ?: ($i % $hallCols) + 1,
                'y' => $seat->position_y ?: intdiv($i, $hallCols) + 1,
                'available' => in_array($seat->id, $available, true),
            ])
            ->all();

        return [
            'rows' => max((int) $event->hall->row_count, (int) collect($seats)->max('y')),
            'cols' => max($hallCols, (int) collect($seats)->max('x')),
            'seats' => $seats,
        ];
    }
}