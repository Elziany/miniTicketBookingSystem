<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Event;
use App\Models\Seat;
use App\Services\EventSeatPriceService;
use App\Services\EventService;
use App\Services\SeatService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order')
                    ->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('event_id')
                            ->label('Event')
                            ->options(
                                app(EventService::class)->getActiveEvents()->pluck('name', 'id')->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                            ViewField::make('seat_ids')
                                ->label('Seats')
                                ->view('seats')
                                ->required(),

                        // Select::make('seat_ids')
                        //     ->label('Seats')
                        //     ->multiple()
                        //     ->searchable()
                        //     ->preload()
                        //     ->live()
                        //     ->options(function ($get) {
                        //         $eventId = $get('event_id');

                        //         if (! $eventId) {
                        //             return [];
                        //         }
                        //         return app(SeatService::class)->getAvailableSeatsByEvent($eventId);
                        //     })
                        //     ->required()
                        //     ->minItems(1),

                        Placeholder::make('reservation_count')
                            ->label('Number of Reservations')
                            ->content(function ($get) {
                                return count($get('seat_ids') ?? []);
                            }),

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
}
