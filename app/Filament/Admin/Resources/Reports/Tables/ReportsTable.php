<?php

namespace App\Filament\Admin\Resources\Reports\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->sortable(),
                TextColumn::make('user.name')->label('Customer'),
                TextColumn::make('technician.name')->label('Technician')->placeholder('-'),
                TextColumn::make('service.name')->label('Service'),
                TextColumn::make('product.name')->label('Product'),
                TextColumn::make('total_price')->label('Total')->money('IDR'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('service_date')->label('Service Date')->date('d M Y'),
                TextColumn::make('created_at')->label('Created')->date('d M Y'),
            ])

            ->filters([
                // Date Range Filter
                Filter::make('date_range')
                    ->form([
                    DatePicker::make('from'),
                    DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('service_date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('service_date', '<=', $date));
                    }),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'assigned' => 'Assigned',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])

            ->actions([]) // no record actions for report
            ->bulkActions([]); // report = read-only
    }
}
