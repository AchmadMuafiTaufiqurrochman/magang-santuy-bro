<?php

namespace App\Filament\Admin\Resources\Orders\Pages;

use App\Filament\Admin\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Populate selected_products dari note untuk ditampilkan di form (sama seperti customer)
        if (isset($data['note']) && str_contains($data['note'], 'PRODUCTS:')) {
            $parts = explode('PRODUCTS:', $data['note'], 2); // Limit to 2 parts
            if (isset($parts[1])) {
                $productIds = json_decode(trim($parts[1]), true);
                if (is_array($productIds)) {
                    // Pastikan product IDs masih valid
                    $validProductIds = \App\Models\Product::whereIn('id', $productIds)
                        ->pluck('id')
                        ->toArray();
                    $data['selected_products'] = $validProductIds;
                }
            }
            // Tampilkan note tanpa product data
            $data['note'] = trim($parts[0]);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
            // Validasi bahwa product IDs yang dipilih valid
            $validProductIds = \App\Models\Product::whereIn('id', $selectedProducts)
                ->pluck('id')
                ->toArray();
            
            if (count($validProductIds) !== count($selectedProducts)) {
                throw ValidationException::withMessages([
                    'selected_products' => 'Some selected products are not valid.',
                ]);
            }

            // Simpan selected products ke dalam note sebagai JSON
            $data['note'] = $originalNote . ' PRODUCTS:' . json_encode($selectedProducts);
        }

        // Handle technician assignment
        if (!empty($data['technician_assignment'])) {
            // Hapus assignment lama jika ada
            $this->record->orderAssignments()->delete();
            
            // Buat assignment baru
            $this->record->orderAssignments()->create([
                'technician_id' => $data['technician_assignment'],
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);
        }

        // Remove fields yang tidak ada di database
        unset($data['price_calculation']);
        unset($data['selected_products']);
        unset($data['technician_assignment']);
        unset($data['current_technician']);

        return $data;
    }
}
