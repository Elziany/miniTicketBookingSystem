<?php

namespace App\Filament\Resources\Events\Tables;

use App\Enum\EventStatus;
use App\Models\Event;
use App\Services\EventService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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
                TextColumn::make('hall.name')->searchable(),
                TextColumn::make('name')->searchable(),
                self::dateColumn('start_time'),
                self::dateColumn('end_time'),
                TextColumn::make('status')->badge(),
                TextColumn::make('approval_mode')->badge(),
                TextColumn::make('approval_window_hours')->numeric()->sortable(),
                TextColumn::make('pricing_mode')->badge(),
                self::dateColumn('started_at'),
                self::dateColumn('ended_at'),
                self::dateColumn('created_at')->toggleable(isToggledHiddenByDefault: true),
                self::dateColumn('updated_at')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([

            ])
            ->recordActions([
                EditAction::make()->modalWidth('4xl'),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Event $record) => ! in_array(
                        $record->status,
                        [EventStatus::CANCELLED, EventStatus::ENDED],
                        true
                    ))
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Event')
                    ->modalDescription('Are you sure you want to cancel this Event?')
                    ->action(fn (Event $record) => app(EventService::class)->cancelEvent($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function dateColumn(string $name): TextColumn
    {
        return TextColumn::make($name)->dateTime()->sortable();
    }
}