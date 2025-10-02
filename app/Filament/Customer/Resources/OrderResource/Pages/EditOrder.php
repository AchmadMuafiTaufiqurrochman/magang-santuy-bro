<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => in_array($this->record->status, ['pending']))
                ->requiresConfirmation()
                ->modalHeading('Delete Order')
                ->modalDescription('Are you sure you want to delete this order? This action cannot be undone.'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Populate selected_products dari note untuk ditampilkan di form
        if (isset($data['note']) && str_contains($data['note'], 'PRODUCTS:')) {
            $parts = explode('PRODUCTS:', $data['note']);
            if (isset($parts[1])) {
                $productIds = json_decode(trim($parts[1]), true);
                if (is_array($productIds)) {
                    $data['selected_products'] = $productIds;
                }
            }
            // Tampilkan note tanpa product data
            $data['note'] = trim($parts[0]);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validasi: minimal harus pilih package atau products (sama seperti CreateOrder)
        if (empty($data['package_id']) && empty($data['selected_products'])) {
            throw ValidationException::withMessages([
                'package_id' => 'You must select at least one service (Package or Individual Products).',
                'selected_products' => 'Please select either a service package or individual products.',
            ]);
        }

        // Pastikan status dan user_id tidak berubah saat edit
        $data['user_id'] = $this->record->user_id;
        $data['status'] = $this->record->status;

        // Gabungkan note dengan selected products info (sama seperti CreateOrder)
        $originalNote = trim($data['note'] ?? '');
        $selectedProducts = $data['selected_products'] ?? [];

        if (!empty($selectedProducts) && is_array($selectedProducts)) {
            // Simpan selected products ke dalam note sebagai JSON
            $data['note'] = $originalNote . ' PRODUCTS:' . json_encode($selectedProducts);
        }

        // Remove fields yang tidak ada di database
        unset($data['price_calculation']);
        unset($data['transaction_status']);
        unset($data['selected_products']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
