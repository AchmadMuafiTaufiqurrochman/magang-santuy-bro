<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Orders')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
            Actions\EditAction::make()
                ->visible(fn (): bool => in_array($this->record->status, ['pending'])),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // === SERVICE INFORMATION ===
                Forms\Components\Select::make('package_id')
                    ->label('📦 Service Package')
                    ->relationship('package', 'name')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder(fn () => $this->record->package ? null : 'No package selected'),

                Forms\Components\Textarea::make('selected_products_display')
                    ->label('🛍️ Selected Products')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(3)
                    ->formatStateUsing(function () {
                        $selectedProducts = $this->record->selectedProducts();
                        if ($selectedProducts->count() > 0) {
                            return $selectedProducts->map(function($product) {
                                return '• ' . $product->name . ' - Rp ' . number_format($product->price, 0, ',', '.');
                            })->implode("\n");
                        }
                        return 'No products selected';
                    })
                    ->visible(fn () => $this->record->selectedProducts()->count() > 0),

                Forms\Components\TextInput::make('total_price_display')
                    ->label('💰 Total Price')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function () {
                        $totalPrice = 0;

                        // Package price
                        if ($this->record->package) {
                            $totalPrice += $this->record->package->price;
                        }

                        // Selected products price
                        $selectedProducts = $this->record->selectedProducts();
                        foreach ($selectedProducts as $product) {
                            $totalPrice += $product->price;
                        }

                        return 'Rp ' . number_format($totalPrice, 0, ',', '.');
                    }),

                // === SCHEDULE & LOCATION ===
                Forms\Components\DatePicker::make('date')
                    ->label('📅 Service Date')
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\TimePicker::make('time_slot')
                    ->label('⏰ Service Time')
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\Textarea::make('address')
                    ->label('📍 Service Address')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(3),

                Forms\Components\Textarea::make('note')
                    ->label('📝 Additional Notes')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(2)
                    ->formatStateUsing(fn () => $this->record->getCleanNoteAttribute() ?: 'No additional notes'),

                // === ORDER STATUS ===
                Forms\Components\TextInput::make('status')
                    ->label('📋 Order Status')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                Forms\Components\TextInput::make('created_at')
                    ->label('🕒 Order Created')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn () => $this->record->created_at?->format('d F Y H:i') ?? 'N/A'),

                Forms\Components\TextInput::make('updated_at')
                    ->label('🔄 Last Updated')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn () => $this->record->updated_at?->format('d F Y H:i') ?? 'N/A'),
            ])
            ->columns(2);
    }


}
