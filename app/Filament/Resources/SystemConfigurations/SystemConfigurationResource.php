<?php

namespace App\Filament\Resources\SystemConfigurations;

use App\Filament\Resources\SystemConfigurations\Pages\CreateSystemConfiguration;
use App\Filament\Resources\SystemConfigurations\Pages\EditSystemConfiguration;
use App\Filament\Resources\SystemConfigurations\Pages\ListSystemConfigurations;
use App\Filament\Resources\SystemConfigurations\Schemas\SystemConfigurationForm;
use App\Filament\Resources\SystemConfigurations\Tables\SystemConfigurationsTable;
use App\Filament\Traits\AuthorizesPermissions;
use App\Models\SystemConfiguration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;

class SystemConfigurationResource extends Resource
{
    use AuthorizesPermissions;
    protected static ?string $model = SystemConfiguration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SystemConfigurationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemConfigurationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    #[Override]
    public static function getPermissionDomain(): string
    {
        return "setting";
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemConfigurations::route('/'),
            'create' => CreateSystemConfiguration::route('/create'),
            'edit' => EditSystemConfiguration::route('/{record}/edit'),
        ];
    }
}
