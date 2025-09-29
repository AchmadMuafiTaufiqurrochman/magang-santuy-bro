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
                'package_id' => 'You must select at least one service (Package or Products).',
            ]);
        }

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // Gabungkan note dengan selected products info
        $originalNote = $data['note'] ?? '';
        $selectedProducts = $data['selected_products'] ?? [];

        if (!empty($selectedProducts)) {
            // Simpan selected products ke dalam note sebagai JSON
            $data['note'] = $originalNote . ' PRODUCTS:' . json_encode($selectedProducts);
        }

        // Remove fields yang tidak ada di database
        unset($data['estimated_price']);
        unset($data['transaction_status']);
        unset($data['selected_products']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
