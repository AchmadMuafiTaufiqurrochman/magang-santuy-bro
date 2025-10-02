<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use App\Models\User;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

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
                    ->label('Package')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('technician.name')
                    ->label('Technician')
                    ->sortable()
                    ->placeholder('Not Assigned'),

                TextColumn::make('order_date')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'in_progress',
                        'success' => 'completed',
                        'danger'  => 'cancelled',
                        'primary' => 'assigned',
                    ])
                    ->sortable(),

                TextColumn::make('total_price')
                    ->money('idr', true),

                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('status')->options([
                    'pending'     => 'Pending',
                    'assigned'    => 'Assigned',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                    'cancelled'   => 'Cancelled',
                ]),
            ])

            ->recordActions([
                Action::make('assign_technician')
                    ->label('Assign Teknisi')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (Order $record): bool =>
                        in_array($record->status, ['pending', 'assigned', 'in_progress'])
                    )
                    ->form([
                        Select::make('technician_id')
                            ->label('Pilih Teknisi')
                            ->placeholder('Pilih teknisi untuk order ini...')
                            ->options(
                                User::where('role', 'technician')
                                    ->where('status', 'active')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data, Order $record): void {
                        // hapus assignment lama
                        $record->orderAssignments()->delete();

                        // buat assignment baru
                        $record->orderAssignments()->create([
                            'technician_id' => $data['technician_id'],
                            'assigned_at'   => now(),
                            'assigned_by'   => Auth::id(),
                        ]);

                        // update status kalau masih pending
                        if ($record->status === 'pending') {
                            $record->update(['status' => 'assigned']);
                        }

                        $technician = User::find($data['technician_id']);

                        Notification::make()
                            ->title('Teknisi Berhasil di-Assign!')
                            ->body("Order #{$record->id} dari {$record->customer->name} telah di-assign ke {$technician->name}")
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
