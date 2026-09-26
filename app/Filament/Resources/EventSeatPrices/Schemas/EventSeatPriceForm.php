<?php

namespace App\Filament\Resources\EventSeatPrices\Schemas;

use App\Enum\SeatType;
use App\Services\SeatService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventSeatPriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->live()
                    ->required(),
                      Select::make('seat_id')
                    ->label('Seat')
                    ->options(function ($get) {

                        $eventId = $get('event_id');

                        if (! $eventId) {
                            return [];
                        }

                        return app(SeatService::class)->getUnpricedSeatsByEvent($eventId);
                    }),
                  Select::make('seat_type')
                    ->options(SeatType::options())
                    ->required()
                    ->default(SeatType::STANDARD->value),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}
