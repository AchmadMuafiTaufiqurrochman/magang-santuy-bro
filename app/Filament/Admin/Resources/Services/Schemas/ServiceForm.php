<?php

namespace App\Filament\Admin\Resources\Services\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;


class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Service Name')
                    ->maxLength(100),

                Textarea::make('description')
                    ->nullable()
                    ->label('Description')
                    ->maxLength(500),

                Select::make('category')
                    ->label('Category')
                    ->options([
                        'maintenance' => 'Maintenance',
                        'repair' => 'Repair',
                        'installation' => 'Installation',
                    ])
                    ->default('maintenance')
                    ->required(),

                TextInput::make('price')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->label('Price')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ]);
    }
}
