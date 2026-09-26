<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enum\EventStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hall_id')
                    ->relationship('hall', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DateTimePicker::make('start_time')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->required(),
                Select::make('status')
                    ->options(EventStatus::options())
                    ->default(EventStatus::UNDER_RESERVATIONS->value)
                    ->required(),
                Select::make('approval_mode')
                    ->options(['auto' => 'Auto', 'manual' => 'Manual'])
                    ->default('auto')
                    ->required(),
                TextInput::make('approval_window_hours')
                    ->numeric(),
                Select::make('pricing_mode')
                    ->options(['per_seat' => 'Per seat', 'per_category' => 'Per category'])
                    ->required(),
                TextInput::make('custom_refund_policy'),
                Textarea::make('cancellation_reason')
                    ->columnSpanFull(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('ended_at'),
            ]);
    }
}
