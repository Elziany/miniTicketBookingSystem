<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Services\ReservationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')
                    ->searchable(),

                TextColumn::make('event.name')
                    ->searchable(),

                TextColumn::make('seat.id')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->searchable(),

                TextColumn::make('reservation_reference')
                    ->searchable(),

                TextColumn::make('price_paid')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge(),

                ImageColumn::make('qr_code_path')
                    ->label('QR Code')
                    ->disk('public')
                    ->size(80),

                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('refunded_amount')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                //
            ])

            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) =>
                        $record->status === 'pending_approval'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Approve reservation')
                    ->modalDescription(
                        'Are you sure you want to approve this reservation?'
                    )
                    ->action(function ($record) {
                        app(ReservationService::class)
                            ->confirmReservation($record);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) =>
                        $record->status === 'pending_approval'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Reject reservation')
                    ->modalDescription(
                        'Are you sure you want to reject this reservation?'
                    )
                    ->action(function ($record) {
                        app(ReservationService::class)
                            ->rejectReservation($record);
                    }),

                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}