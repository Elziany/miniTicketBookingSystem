<?php

namespace App\Filament\Resources\EventSeatPrices;

use App\Filament\Resources\EventSeatPrices\Pages\CreateEventSeatPrice;
use App\Filament\Resources\EventSeatPrices\Pages\EditEventSeatPrice;
use App\Filament\Resources\EventSeatPrices\Pages\ListEventSeatPrices;
use App\Filament\Resources\EventSeatPrices\Schemas\EventSeatPriceForm;
use App\Filament\Resources\EventSeatPrices\Tables\EventSeatPricesTable;
use App\Models\EventSeatPrice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EventSeatPriceResource extends Resource
{
    protected static ?string $model = EventSeatPrice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = "Event Management";
    protected static ?int $navigationSort = 3;

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
 
    public static function getPages(): array
    {
        return [
            'index' => ListEventSeatPrices::route('/'),
            'create' => CreateEventSeatPrice::route('/create'),
            'edit' => EditEventSeatPrice::route('/{record}/edit'),
        ];
    }
}
