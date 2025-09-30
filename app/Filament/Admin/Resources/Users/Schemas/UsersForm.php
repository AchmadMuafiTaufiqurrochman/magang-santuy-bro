<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class UsersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Name')
                    ->maxLength(100),

                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true) // biar email tidak duplikat
                    ->label('Email')
                    ->maxLength(100),

                TextInput::make('phone')
                    ->required()
                    ->label('Phone')
                    ->maxLength(20)
                    ->unique('users', 'phone', ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null) // hash password
                    ->dehydrated(fn ($state) => filled($state)) // hanya simpan kalau diisi
                    ->required(fn (string $context): bool => $context === 'create'),

                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                        'technician' => 'Technician',
                    ])
                    ->default('customer')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
