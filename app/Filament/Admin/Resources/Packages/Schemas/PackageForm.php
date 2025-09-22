<?php

namespace App\Filament\Admin\Resources\Packages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->label('Nama Paket')
                ->placeholder('Masukkan nama paket')
                ->required()
                ->default(''),

            Textarea::make('description')
                ->label('Deskripsi')
                ->placeholder('Masukkan deskripsi paket')
                ->default(''),

            TextInput::make('price')
                ->label('Harga')
                ->numeric()
                ->prefix('Rp')
                ->default(0.00)
                ->required(),
        ]);
        }
}
