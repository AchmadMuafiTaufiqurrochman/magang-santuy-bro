<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => in_array($this->record->status, ['pending'])),
        ];
    }
    
    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Infolists\Components\Section::make('Order Information')
                ->schema([
                    Infolists\Components\TextEntry::make('id')
                        ->label('Order ID'),
                    Infolists\Components\TextEntry::make('paket.name')
                        ->label('Service Package'),
                    Infolists\Components\TextEntry::make('paket.product.name')
                        ->label('Product'),
                    Infolists\Components\TextEntry::make('date')
                        ->date('d F Y'),
                    Infolists\Components\TextEntry::make('time_slot')
                        ->time('H:i'),
                    Infolists\Components\TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'assigned' => 'info',
                            'in_progress' => 'primary',
                            'done' => 'success',
                            'cancelled' => 'danger',
                        }),
                ])->columns(2),
                
            Infolists\Components\Section::make('Service Details')
                ->schema([
                    Infolists\Components\TextEntry::make('address')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('note')
                        ->label('Additional Notes')
                        ->columnSpanFull()
                        ->placeholder('No additional notes'),
                ])->columns(2),
                
            Infolists\Components\Section::make('Payment Information')
                ->schema([
                    Infolists\Components\TextEntry::make('paket.price')
                        ->label('Service Price')
                        ->money('IDR'),
                    Infolists\Components\TextEntry::make('transaction.payment_method')
                        ->label('Payment Method')
                        ->formatStateUsing(fn ($state) => $state ? strtoupper($state) : 'Not Set'),
                    Infolists\Components\TextEntry::make('transaction.status')
                        ->label('Payment Status')
                        ->badge()
                        ->color(fn ($state): string => match ($state) {
                            'pending' => 'warning',
                            'paid' => 'success',
                            'failed' => 'danger',
                            'cancelled' => 'gray',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'No Payment'),
                ])->columns(2),
        ]);
    }
}
