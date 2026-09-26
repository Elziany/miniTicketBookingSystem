<?php

namespace App\Filament\Resources\Events\Tables;

use App\Enum\EventStatus;
use App\Services\EventService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hall.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('approval_mode')
                    ->badge(),
                TextColumn::make('approval_window_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pricing_mode')
                    ->badge(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ended_at')
                    ->dateTime()
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
                EditAction::make(),
                Action::make('cancel')
                ->label('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) =>
                    !in_array($record->status , [EventStatus::CANCELLED , EventStatus::ENDED])
                )
                ->requiresConfirmation()
                ->modalHeading('Approve reservation')
                ->modalDescription(
                    'Are you sure you want to cancel this Event?'
                )
                ->action(function ($record) {
                    app(EventService::class)
                        ->cancelEvent($record);
                })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
