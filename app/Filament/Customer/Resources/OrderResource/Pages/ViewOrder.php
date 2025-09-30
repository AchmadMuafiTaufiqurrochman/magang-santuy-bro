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
                // === SERVICE SELECTION === (sama seperti di create)
                Forms\Components\TextInput::make('package_display')
                    ->label('Service Package (Optional)')
                    ->placeholder('Select a service package...')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function () {
                        if ($this->record->package) {
                            return $this->record->package->name . ' - Rp ' . number_format($this->record->package->price, 0, ',', '.');
                        }
                        return 'No package selected';
                    })
                    ->helperText('Choose a service package or skip to select individual products'),

                Forms\Components\Textarea::make('selected_products_display')
                    ->label('Individual Products (Optional)')
                    ->placeholder('Select individual products...')
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
                        return 'No individual products selected';
                    })
                    ->helperText('Select multiple products - hold Ctrl/Cmd to select multiple items'),

                // === SCHEDULE & LOCATION === (sama seperti di create)
                Forms\Components\DatePicker::make('date')
                    ->label('Service Date')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Select your preferred service date'),

                Forms\Components\TimePicker::make('time_slot')
                    ->label('Preferred Time')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Choose your preferred time slot'),

                Forms\Components\Textarea::make('address')
                    ->label('Service Address')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(3)
                    ->placeholder('Please provide your complete address for service delivery...'),

                Forms\Components\Textarea::make('note')
                    ->label('Additional Notes')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(2)
                    ->formatStateUsing(fn () => $this->record->getCleanNoteAttribute() ?: '')
                    ->placeholder('Any special instructions or requests...'),

                // === ORDER SUMMARY === (sama seperti di create)
                Forms\Components\TextInput::make('price_calculation')
                    ->label('� Total Price')
                    ->disabled()
                    ->dehydrated(false)
                    ->prefix('Total:')
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

                        return $totalPrice > 0 ? "Rp " . number_format($totalPrice, 0, ',', '.') : 'Rp 0';
                    }),

                // === ORDER STATUS === (info tambahan untuk view)
                Forms\Components\TextInput::make('status')
                    ->label('Order Status')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                Forms\Components\TextInput::make('created_at')
                    ->label('Order Created')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn () => $this->record->created_at?->format('d F Y H:i') ?? 'N/A'),
            ])
            ->columns(2);
    }


}
