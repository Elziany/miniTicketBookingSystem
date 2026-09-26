<?php

namespace App\Filament\Resources\SystemConfigurations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SystemConfigurationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hold_duration_minutes')
                    ->required(),
                TextInput::make('value')
                    ->required(),
            ]);
    }
}
