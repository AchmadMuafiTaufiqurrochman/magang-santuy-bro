<?php

namespace App\Filament\Admin\Resources\Reports\Tables;

use App\Models\Order;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['user', 'technician', 'service'])
            )

            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('technician.name')
                    ->label('Technician')
                    ->placeholder('Belum Ditentukan'),

                TextColumn::make('service.name')
                    ->label('Service'),

                TextColumn::make('service_date')
                    ->label('Service Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'assigned',
                        'info' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i'),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Order')
                    ->options([
                        'pending' => 'Pending',
                        'assigned' => 'Assigned',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\Filter::make('service_date')
                    ->label('Periode Layanan')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('to')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($q) => $q->whereDate('service_date', '>=', $data['from'])
                            )
                            ->when(
                                $data['to'],
                                fn ($q) => $q->whereDate('service_date', '<=', $data['to'])
                            );
                    }),
            ])

            // ❌ REPORT TIDAK MEMERLUKAN EDIT / DELETE
            ->recordActions([])

            ->toolbarActions([])

            ->defaultSort('created_at', 'desc');
    }
}
