<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Enums\FontWeight;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),

                // KOLOM SERVICE PACKAGE (seperti di customer order)
                TextColumn::make('package')
                    ->label('Service Package')
                    ->getStateUsing(function ($record) {
                        if ($record->package) {
                            $price = number_format($record->package->price, 0, ',', '.');
                            return $record->package->name . "\nRp " . $price;
                        }
                        return 'No Package';
                    })
                    ->wrap()
                    ->searchable(),

                // KOLOM INDIVIDUAL PRODUCTS (seperti di customer order)  
                TextColumn::make('products')
                    ->label('Individual Products')
                    ->getStateUsing(function ($record) {
                        $selectedProducts = $record->selectedProducts();
                        if ($selectedProducts->count() > 0) {
                            return $selectedProducts->map(function($product) {
                                $price = number_format($product->price, 0, ',', '.');
                                return $product->name . ' (Rp ' . $price . ')';
                            })->implode("\n");
                        }
                        return 'No Products';
                    })
                    ->wrap()
                    ->searchable(),

                // KOLOM SERVICE DATE (seperti di customer order)
                TextColumn::make('date')
                    ->label('Service Date')
                    ->date('d M Y')
                    ->sortable(),

                // KOLOM PREFERRED TIME (seperti di customer order)  
                TextColumn::make('time_slot')
                    ->label('Preferred Time')
                    ->time('H:i')
                    ->sortable(),

                // KOLOM SERVICE ADDRESS (seperti di customer order)
                TextColumn::make('address')
                    ->label('Service Address')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap()
                    ->searchable(),

                // KOLOM 💰 TOTAL PRICE (seperti di customer order)
                TextColumn::make('total_price')
                    ->label('💰 Total Price')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        $total = 0;

                        // Harga package jika ada
                        if ($record->package) {
                            $total += $record->package->price;
                        }

                        // Harga selected products jika ada
                        $selectedProducts = $record->selectedProducts();
                        foreach ($selectedProducts as $product) {
                            $total += $product->price;
                        }

                        return $total;
                    }),

                // KOLOM ADDITIONAL NOTES (seperti di customer order)
                TextColumn::make('note')
                    ->label('Additional Notes')
                    ->getStateUsing(function ($record) {
                        return $record->getCleanNoteAttribute() ?: 'No notes';
                    })
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30 || $state === 'No notes') {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('technician_name')
                    ->label('Technician')
                    ->getStateUsing(function ($record) {
                        $assignment = $record->orderAssignments()->with('technician')->first();
                        return $assignment?->technician?->name ?? 'Belum Assign';
                    }),
                    
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                    'warning' => 'pending',
                    'info' => 'in_progress',
                    'success' => 'done',
                ]),

                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
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

