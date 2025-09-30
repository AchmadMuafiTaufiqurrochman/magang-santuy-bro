<?php

namespace App\Filament\Admin\Resources\Orders\Pages;

use App\Filament\Admin\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi: minimal harus pilih package atau products (sama seperti customer)
        if (empty($data['package_id']) && empty($data['selected_products'])) {
            throw ValidationException::withMessages([
                'package_id' => 'You must select at least one service (Package or Individual Products).',
                'selected_products' => 'Please select either a service package or individual products.',
            ]);
        }

        // Gabungkan note dengan selected products info (sama seperti customer)
        $originalNote = trim($data['note'] ?? '');
        $selectedProducts = $data['selected_products'] ?? [];

        if (!empty($selectedProducts) && is_array($selectedProducts)) {
            // Simpan selected products ke dalam note sebagai JSON
            $data['note'] = $originalNote . ' PRODUCTS:' . json_encode($selectedProducts);
        }

        // Remove fields yang tidak ada di database
        unset($data['price_calculation']);
        unset($data['selected_products']);
        unset($data['technician_assignment']);
        unset($data['current_technician']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Handle technician assignment setelah order dibuat
        $technicianId = $this->form->getState()['technician_assignment'] ?? null;
        
        if ($technicianId && in_array($this->record->status, ['assigned', 'in_progress'])) {
            $this->record->orderAssignments()->create([
                'technician_id' => $technicianId,
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);
        }
    }
}
