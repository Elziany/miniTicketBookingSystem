<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('reservation_id')
                    ->relationship('reservation', 'id')
                    ->required(),
                TextInput::make('checked_in_by')
                    ->required()
                    ->numeric(),
                Select::make('check_in_type')
                    ->options(['qr_scan' => 'Qr scan', 'manual' => 'Manual'])
                    ->required(),
                Textarea::make('manual_check_in_note')
                    ->columnSpanFull(),
                DateTimePicker::make('checked_in_at')
                    ->required(),
            ]);
    }
}
