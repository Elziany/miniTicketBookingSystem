<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required(),
                Select::make('seat_id')
                    ->relationship('seat', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('reservation_reference')
                    ->required(),
                TextInput::make('price_paid')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'held' => 'Held',
            'pending_approval' => 'Pending approval',
            'confirmed' => 'Confirmed',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ])
                    ->required(),
                TextInput::make('qr_code_path'),
                DateTimePicker::make('expires_at'),
                TextInput::make('refunded_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Textarea::make('cancellation_reason')
                    ->columnSpanFull(),
                Textarea::make('rejection_reason')
                    ->columnSpanFull(),
            ]);
    }
}
