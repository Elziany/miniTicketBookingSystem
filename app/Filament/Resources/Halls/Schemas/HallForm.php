<?php

namespace App\Filament\Resources\Halls\Schemas;

use App\Models\Hall;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class HallForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required(),

            Textarea::make('address')
                ->columnSpanFull(),

            Select::make('layout_type')
                ->options(['grid' => 'Grid', 'free_form' => 'Free form'])
                ->default('grid')
                ->live()
                ->required(),

            TextInput::make('row_count')
                ->label('Rows')
                ->numeric()
                ->minValue(1)
                ->maxValue(26) // rows are lettered A-Z
                ->live(onBlur: true)
                ->required(fn (Get $get) => $get('layout_type') === 'grid'),

            TextInput::make('column_count')
                ->label('Columns')
                ->numeric()
                ->minValue(1)
                ->maxValue(30)
                ->live(onBlur: true)
                ->required(fn (Get $get) => $get('layout_type') === 'grid'),

            Section::make('Seat layout')
                ->description('Leave a box empty to use the default label (A1, A2, ...).')
                ->columnSpanFull()
                ->visible(fn (Get $get) => $get('layout_type') === 'grid'
                    && (int) $get('row_count') > 0
                    && (int) $get('column_count') > 0)
                ->schema([
                    Grid::make()
                        ->columns(fn (Get $get) => max(1, min(30, (int) $get('column_count'))))
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
                                        ->extraInputAttributes(['class' => 'text-center']);
                                }
                            }

                            return $fields;
                        }),
                ]),
        ]);
    }
}