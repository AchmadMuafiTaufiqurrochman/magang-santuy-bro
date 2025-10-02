<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
<<<<<<< HEAD
use Filament\Tables\Filters\SelectFilter;
=======
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Select;
use App\Models\User;
use Filament\Notifications\Notification;
>>>>>>> d74af388c4b535315f1ec848e10a72ca08f85ea4

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
<<<<<<< HEAD
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
=======
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
                    ->label('Teknisi')
                    ->getStateUsing(function ($record) {
                        $assignment = $record->orderAssignments()->with('technician')->first();
                        if ($assignment && $assignment->technician) {
                            return $assignment->technician->name;
                        }
                        return 'Belum di-Assign';
                    })
                    ->color(function ($record) {
                        $assignment = $record->orderAssignments()->with('technician')->first();
                        return $assignment ? 'success' : 'danger';
                    })
                    ->weight(function ($record) {
                        $assignment = $record->orderAssignments()->with('technician')->first();
                        return $assignment ? FontWeight::Medium : FontWeight::Bold;
                    }),
                    
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'assigned',
                        'info' => 'in_progress',
                        'success' => 'done',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
>>>>>>> d74af388c4b535315f1ec848e10a72ca08f85ea4
            ->recordActions([
                Action::make('assign_technician')
                    ->label('Assign Teknisi')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'assigned', 'in_progress']))
                    ->form([
                        Select::make('technician_id')
                            ->label('Pilih Teknisi')
                            ->placeholder('Pilih teknisi untuk order ini...')
                            ->options(function() {
                                return User::where('role', 'technician')
                                    ->where('status', 'active')
                                    ->get()
                                    ->mapWithKeys(function($tech) {
                                        return [$tech->id => $tech->name . ' (' . $tech->email . ')'];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        // Hapus assignment lama jika ada
                        $record->orderAssignments()->delete();
                        
                        // Buat assignment baru
                        $record->orderAssignments()->create([
                            'technician_id' => $data['technician_id'],
                            'assigned_at' => now(),
                            'assigned_by' => auth()->id(),
                        ]);

                        // Update status order ke assigned jika masih pending
                        if ($record->status === 'pending') {
                            $record->update(['status' => 'assigned']);
                        }

                        $technician = User::find($data['technician_id']);
                        
                        Notification::make()
                            ->title('Teknisi Berhasil di-Assign!')
                            ->body("Order #{$record->id} dari {$record->user->name} telah di-assign ke {$technician->name}")
                            ->success()
                            ->send();
                    }),
                    
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}


