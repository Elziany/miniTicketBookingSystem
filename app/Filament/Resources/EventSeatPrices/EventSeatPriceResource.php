<?php

namespace App\Filament\Resources\EventSeatPrices;

use App\Filament\Resources\EventSeatPrices\Pages\CreateEventSeatPrice;
use App\Filament\Resources\EventSeatPrices\Pages\EditEventSeatPrice;
use App\Filament\Resources\EventSeatPrices\Pages\ListEventSeatPrices;
use App\Filament\Resources\EventSeatPrices\Schemas\EventSeatPriceForm;
use App\Filament\Resources\EventSeatPrices\Tables\EventSeatPricesTable;
use App\Filament\Traits\AuthorizesPermissions;
use App\Models\EventSeatPrice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventSeatPriceResource extends Resource
{
    use AuthorizesPermissions;
    protected static ?string $model = EventSeatPrice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EventSeatPriceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventSeatPricesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getPermissionDomain(): string{
        return 'event_price';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventSeatPrices::route('/'),
            'create' => CreateEventSeatPrice::route('/create'),
            'edit' => EditEventSeatPrice::route('/{record}/edit'),
        ];
    }
}
