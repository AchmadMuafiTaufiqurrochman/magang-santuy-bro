<?php

namespace App\Filament\Technician\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Livewire\Attributes\Computed;

class Dashboard extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.technician.pages.dashboard';

    protected static ?string $title = 'Dashboard Technician';

    #[Computed]
    public function isAvailable(): bool
    {
        return auth()->user()->technician_status === 'available';
    }

    public function toggleAvailability(): void
    {
        $user = auth()->user();
        $currentStatus = $user->technician_status ?? 'offline';

        $newStatus = $currentStatus === 'available' ? 'offline' : 'available';

        $user->update(['technician_status' => $newStatus]);

        // Refresh the component
        $this->dispatch('$refresh');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // No widgets - keep it simple
        ];
    }
}
