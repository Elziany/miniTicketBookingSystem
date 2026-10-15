<?php

namespace App\Filament\Resources\Halls\Schemas;

use App\Models\Hall;
use App\Services\UserService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class HallForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Hall details')
                    ->description('Basic information about this hall.')
                    ->icon(Heroicon::BuildingOffice2)
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Hall name')
                            ->placeholder('e.g. Main Hall')
                            ->prefixIcon(Heroicon::Tag)
                            ->maxLength(255)
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Street, city, landmarks...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Staff')
                    ->description('Choose the agents working in this hall, then pick a manager from them.')
                    ->icon(Heroicon::UserGroup)
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('agents')
                            ->label('Agents')
                            ->relationship('agents', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live() 
                            ->placeholder('Select agents')
                            ->columnSpan(1),

                        Select::make('manager')
                            ->label('Hall Manager')
                            ->options(function (Get $get) {
                                $agentIds = $get('agents') ?? [];

                                return app(UserService::class)
                                    ->getStaffUsers()
                                    ->whereIn('id', $agentIds)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->placeholder('Select a manager')
                            ->helperText('Only selected agents are available here.')
                            ->columnSpan(1),
                    ]),

                Section::make('Seating layout')
                    ->description('Define how seats are arranged in this hall.')
                    ->icon(Heroicon::Squares2x2)
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('layout_type')
                            ->label('Layout type')
                            ->options(['grid' => 'Grid', 'free_form' => 'Free form'])
                            ->default('grid')
                            ->live()
                            ->required()
                            ->native(false)
                            ->helperText('Grid = fixed rows & columns. Free form = custom placement.'),

                        TextInput::make('row_count')
                            ->label('Rows')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(26)
                            ->suffix('max 26')
                            ->prefixIcon(Heroicon::Bars3)
                            ->live(onBlur: true)
                            ->required(fn(Get $get) => $get('layout_type') === 'grid')
                            ->visible(fn(Get $get) => $get('layout_type') === 'grid'),

                        TextInput::make('column_count')
                            ->label('Columns')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('max 30')
                            ->prefixIcon(Heroicon::ViewColumns)
                            ->live(onBlur: true)
                            ->required(fn(Get $get) => $get('layout_type') === 'grid')
                            ->visible(fn(Get $get) => $get('layout_type') === 'grid'),
                    ]),

                Section::make('Seat labels')
                    ->description('Leave a box empty to use the default label (A1, A2, ...).')
                    ->icon(Heroicon::Ticket)
                    ->collapsible()
                    ->columnSpanFull()
                    ->visible(fn(Get $get) => $get('layout_type') === 'grid'
                        && (int) $get('row_count') > 0
                        && (int) $get('column_count') > 0)
                    ->schema([
                        Grid::make()
                            ->columns(fn(Get $get) => max(1, min(30, (int) $get('column_count'))))
                            ->extraAttributes(['class' => 'gap-2 overflow-x-auto pb-2'])
                            ->schema(function (Get $get) {
                                $rows = max(0, min(26, (int) $get('row_count')));
                                $cols = max(0, min(30, (int) $get('column_count')));
                                $fields = [];

                                for ($r = 0; $r < $rows; $r++) {
                                    for ($c = 0; $c < $cols; $c++) {
                                        $fields[] = TextInput::make("seat_labels.{$r}_{$c}")
                                            ->hiddenLabel()
                                            ->placeholder(Hall::defaultSeatLabel($r, $c))
                                            ->maxLength(20)
                                            ->dehydrated(false)
                                            ->extraInputAttributes([
                                                'class' => 'text-center text-sm font-medium',
                                            ]);
                                    }
                                }

                                return $fields;
                            }),
                    ]),
            ]);
    }
}