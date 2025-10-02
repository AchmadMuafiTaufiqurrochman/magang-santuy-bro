<?php

namespace App\Filament\Admin\Resources\Schedules\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label('Order')
                    ->relationship('order', 'id')
                    ->required(),

                Select::make('technician_id')
                    ->label('Technician')
                    ->relationship('technician', 'name')
                    ->required(),

                DatePicker::make('scheduled_date')
                    ->label('Scheduled Date')
                    ->required(),
                TimePicker::make('scheduled_time')
                    ->label('Scheduled Time')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->nullable(),
            ]);
    }
}
