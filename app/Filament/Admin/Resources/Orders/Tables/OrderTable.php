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

                TextColumn::make('user.name')
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

                TextColumn::make('package.name')
                    ->label('Package')
                    ->placeholder('-'),

                // tampilkan teknisi dari relasi orderAssignments
                TextColumn::make('orderAssignments.technician.name')
                    ->label('Technician')
                    ->formatStateUsing(fn ($state) => $state ?: 'Not Assigned')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'assigned',
                        'info'    => 'in_progress',
                        'success' => 'done',
                        'danger'  => 'cancelled',
                    ])
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->money('idr', true),
            ])

            ->filters([
                SelectFilter::make('status')->options([
                    'pending'     => 'Pending',
                    'assigned'    => 'Assigned',
                    'in_progress' => 'In Progress',
                    'done'        => 'Done',
                    'cancelled'   => 'Cancelled',
                ]),
            ])

            ->recordActions([
                Action::make('assign_technician')
                    ->label('Assign Technician')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    // hanya tampil jika belum ada teknisi assigned
                    ->visible(fn (Order $record): bool =>
                        !$record->orderAssignments()->exists() &&
                        $record->status === 'pending'
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
                        $record->orderAssignments()->create([
                            'technician_id' => $data['technician_id'],
                            'assigned_at'   => now(),
                            'assigned_by'   => Auth::id(),
                        ]);

                        $record->update(['status' => 'assigned']);

                        $technician = User::find($data['technician_id']);

                        Notification::make()
                            ->title('Teknisi Berhasil di-Assign!')
                            ->body("Order #{$record->id} dari {$record->user->name} telah di-assign ke {$technician->name}")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteBulkAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
