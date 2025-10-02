<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('id')->sortable(),

            TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

            TextColumn::make('service.name')
                ->label('Service')
                ->sortable()
                ->searchable(),

            TextColumn::make('product.name')
                ->label('Product')
                ->sortable()
                ->searchable(),

            TextColumn::make('package')
                ->label('package')
                ->sortable()
                ->searchable(),

            TextColumn::make('technician.name')
                ->label('Technician')
                ->sortable()
                ->default('Not Assigned'),

            TextColumn::make('order_date')
                ->dateTime('d M Y H:i'),

            TextColumn::make('status')
                ->badge()
                ->colors([
                    'warning' => 'pending',
                    'info'    => 'in_progress',
                    'success' => 'completed',
                    'danger'  => 'cancelled',
                ])
                ->sortable(),

            TextColumn::make('total_price')
                ->money('idr', true),

            TextColumn::make('created_at')
                ->dateTime('d M Y H:i')
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            SelectFilter::make('status')
                ->options([
                    'pending'     => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                    'cancelled'   => 'Cancelled',
                ]),
        ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}


