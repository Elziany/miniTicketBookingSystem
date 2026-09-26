<?php

namespace App\Filament\Resources\EventFeedback;

use App\Filament\Resources\EventFeedback\Pages\CreateEventFeedback;
use App\Filament\Resources\EventFeedback\Pages\EditEventFeedback;
use App\Filament\Resources\EventFeedback\Pages\ListEventFeedback;
use App\Filament\Resources\EventFeedback\Schemas\EventFeedbackForm;
use App\Filament\Resources\EventFeedback\Tables\EventFeedbackTable;
use App\Filament\Traits\AuthorizesPermissions;
use App\Models\EventFeedback;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventFeedbackResource extends Resource
{
    use AuthorizesPermissions ;
    protected static ?string $model = EventFeedback::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EventFeedbackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventFeedbackTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPermissionDomain(): string
    {
        return 'feedback';
    }
    public static function getPages(): array
    {
        return [
            'index' => ListEventFeedback::route('/'),
            'create' => CreateEventFeedback::route('/create'),
            'edit' => EditEventFeedback::route('/{record}/edit'),
        ];
    }
}
