<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi: minimal harus pilih package atau products
        if (empty($data['package_id']) && empty($data['selected_products'])) {
            throw ValidationException::withMessages([
                'package_id' => 'You must select at least one service (Package or Individual Products).',
                'selected_products' => 'Please select either a service package or individual products.',
            ]);
        }

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // Gabungkan notes dengan selected products info
        $originalNote = trim($data['notes'] ?? '');
        $selectedProducts = $data['selected_products'] ?? [];

        if (!empty($selectedProducts) && is_array($selectedProducts)) {
            // Simpan selected products ke dalam notes sebagai JSON
            $data['notes'] = $originalNote . ' PRODUCTS:' . json_encode($selectedProducts);
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
