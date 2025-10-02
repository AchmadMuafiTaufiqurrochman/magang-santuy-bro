<?php

namespace App\Filament\Admin\Resources\Schedules\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SchedulesTable extends Table
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),

                TextColumn::make('order.id')
                    ->label('Order ID'),

                TextColumn::make('technician.name')
                    ->label('Technician'),

                TextColumn::make('scheduled_date')
                    ->date()
                    ->label('Date'),

                TextColumn::make('scheduled_time')
                    ->time()
                    ->label('Time'),

                TextColumn::make('status')
                    ->badge()
                    ->labels([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'confirmed',
                        'success' => 'completed',
                        'danger'  => 'cancelled',
                    ])
                    ->sortable(),

                TextColumn::make('notes')
                    ->limit(30)
                    ->label('Notes'),
            ])
            ->filters([
                //
            ]);
    }
}
