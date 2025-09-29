<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),

                Select::make('technician_id')
                    ->relationship('technician', 'name'),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ])
                    ->default('pending'),
            ]);
    }
}
