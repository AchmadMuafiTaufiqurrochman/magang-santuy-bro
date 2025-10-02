<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;


class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                ->label('Customer')
                ->relationship('customer', 'name')
                ->searchable()
                ->required(),

            Select::make('service_id')
                ->label('Service')
                ->relationship('service', 'name')
                ->searchable()
                ->required(),

            Select::make('technician_id')
                ->label('Technician')
                ->relationship('technician', 'name')
                ->searchable()
                ->nullable(),

            DateTimePicker::make('order_date')
                ->label('Order Date')
                ->required(),

            Select::make('status')
                ->options([
                    'pending'     => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                    'cancelled'   => 'Cancelled',
                ])
                ->default('pending')
                ->required(),

            TextInput::make('total_price')
                ->numeric()
                ->prefix('Rp')
                ->required(),

            Textarea::make('notes')
                ->label('Notes')
                ->nullable()
                ->columnSpanFull(),
            ]);
    }
}
