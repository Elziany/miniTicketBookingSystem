<?php

namespace App\Filament\Resources\Staff\Schemas;

use App\Enum\UserType;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                Select::make('manager_id')
                    ->label('Direct Manager')
                    ->options(function (?User $record) {
                        return app(UserService::class)
                            ->getStaffUsers($record?->id)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder('Select direct manager (if any)'),
                Select::make('type')
                    ->label('type')
                    ->options(UserType::class)
                    ->required(),
                Textarea::make('phone')
                    ->columnSpanFull(),
                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
            ]);
    }
}
