<?php

namespace App\Filament\Resources\Halls\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HallForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('address')
                    ->columnSpanFull(),
                Select::make('layout_type')
                    ->options(['grid' => 'Grid', 'free_form' => 'Free form'])
                    ->required(),
                TextInput::make('row_count')
                    ->numeric(),
                TextInput::make('column_count')
                    ->numeric(),
            ]);
    }
}
