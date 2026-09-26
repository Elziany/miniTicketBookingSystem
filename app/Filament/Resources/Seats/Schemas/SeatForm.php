<?php

namespace App\Filament\Resources\Seats\Schemas;

use App\Enum\SeatType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;


class SeatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hall_id')
                    ->relationship('hall', 'name')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('position_x')
                    ->numeric(),
                TextInput::make('position_y')
                    ->numeric(),
            ]);
    }
}
