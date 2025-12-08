<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('technician.name')
                    ->label('Technician')
                    ->placeholder('Belum di-Assign')
                    ->color(fn($record) => $record->technician ? 'success' : 'danger')
                    ->weight(FontWeight::Medium),

                TextColumn::make('service.name')
                    ->label('Service')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable(),
                TextColumn::make('package.name')
                    ->label('Package')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('service_date')
                    ->label('Service Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('time_slot')
                    ->label('Time Slot')
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(40)
                    ->tooltip(fn($state) => strlen($state) > 40 ? $state : null),

                TextColumn::make('note')
                    ->label('Note')
                    ->limit(30)
                    ->tooltip(fn($state) => strlen($state) > 30 ? $state : null)
                    ->placeholder('-'),

                TextColumn::make('total_price')
                    ->label(' Total')
                    ->money('IDR')
                    ->color('success')
                    ->weight(FontWeight::Bold)
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

                TextColumn::make('created_at')->label('Created')->dateTime('d M Y H:i'),
            ])

            ->recordActions([
                /**
                 * 🔧 Tombol Assign Teknisi
                 */
                Action::make('assign_technician')
                    ->label(fn($record) =>
                        $record->technician
                            ? '✔ Teknisi Ter-assign'
                            : 'Assign Teknisi'
                    )
                    ->icon(fn($record) =>
                        $record->technician
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-user-plus'
                    )
                    ->color(fn($record) =>
                        $record->technician
                            ? 'success'
                            : 'info'
                    )
                    ->disabled(fn($record) => $record->technician !== null) // tidak bisa ditekan lagi jika sudah assign
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'assigned', 'in_progress']))
                    ->form([
                        Select::make('technician_id')
                            ->label('Pilih Teknisi')
                            ->placeholder('Pilih teknisi...')
                            ->options(
                                User::where('role', 'technician')
                                    ->where('status', 'active')
                                    ->get()
                                    ->mapWithKeys(fn($tech) => [
                                        $tech->id => "{$tech->name} ({$tech->email})"
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data, Order $record): void {
                        // Update order dengan technician_id dan status
                        $record->update([
                            'technician_id' => $data['technician_id'],
                            'status' => $record->status === 'pending' ? 'assigned' : $record->status,
                        ]);

                        // Buat OrderAssignment agar order muncul di dashboard teknisi
                        \App\Models\OrderAssignment::create([
                            'order_id' => $record->id,
                            'technician_id' => $data['technician_id'],
                            'assigned_by' => Auth::id(),
                            'assigned_at' => now(),
                        ]);

                        $technician = User::find($data['technician_id']);

                        Notification::make()
                            ->title('Teknisi Berhasil di-Assign ✅')
                            ->body("Order #{$record->id} dari {$record->user->name} telah di-assign ke {$technician->name}.")
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
