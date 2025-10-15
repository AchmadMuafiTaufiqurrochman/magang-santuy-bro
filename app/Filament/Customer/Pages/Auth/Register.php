<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;

class Register extends Page
{
    protected string $view = 'filament.pages.auth.register';

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Full Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email Address')
                ->email()
                ->unique(User::class, 'email')
                ->required(),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->minLength(6)
                ->same('password_confirmation'),

            TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->required(),

            // Optional: Role jika kamu ingin user memilih role
            Select::make('role')
                ->options([
                    'customer' => 'Customer',
                    'technician' => 'Technician',
                ])
                ->default('customer')
                ->required(),
        ];
    }

    protected function handleRegistration(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'customer',
            'status' => 'active',
        ]);
    }
}
