<?php

namespace App\Filament\Technician\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Livewire\Attributes\Computed;
use App\Models\OrderAssignment;
use Filament\Notifications\Notification;

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

    #[Computed]
    public function todayOrders()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereDate('assigned_at', today())
            ->with('order.user', 'order.package')
            ->get();
    }

    #[Computed] 
    public function completedToday()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereHas('order', function($query) {
                $query->where('status', 'done');
            })
            ->whereDate('assigned_at', today())
            ->count();
    }

    #[Computed]
    public function weekCount()
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereBetween('assigned_at', [$weekStart, $weekEnd])
            ->count();
    }

    #[Computed]
    public function monthCount()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereMonth('assigned_at', now()->month)
            ->count();
    }

    #[Computed]
    public function recentActivity()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->with('order.package', 'order.user')
            ->orderBy('assigned_at', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function pendingOrders()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereHas('order', function($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            })
            ->with('order.user', 'order.package')
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    #[Computed] 
    public function newAssignments()
    {
        // Orders yang baru di-assign dalam 24 jam terakhir dan belum di-accept
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereHas('order', function($query) {
                $query->where('status', 'assigned');
            })
            ->where('assigned_at', '>=', now()->subDay())
            ->with('order.user', 'order.package')
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    public function acceptOrder($assignmentId)
    {
        $assignment = \App\Models\OrderAssignment::where('id', $assignmentId)
            ->where('technician_id', auth()->id())
            ->first();

        if ($assignment) {
            $assignment->order->update(['status' => 'in_progress']);
            
            \Filament\Notifications\Notification::make()
                ->title('Order Accepted!')
                ->body("Order #{$assignment->order->id} has been accepted and is now in progress.")
                ->success()
                ->send();

            // Refresh the component
            $this->dispatch('$refresh');
        }
    }

    public function rejectOrder($assignmentId)
    {
        $assignment = \App\Models\OrderAssignment::where('id', $assignmentId)
            ->where('technician_id', auth()->id())
            ->first();

        if ($assignment) {
            // Delete the assignment and reset order status to pending
            $assignment->order->update(['status' => 'pending']);
            $assignment->delete();
            
            \Filament\Notifications\Notification::make()
                ->title('Order Rejected')
                ->body("Order #{$assignment->order->id} has been rejected and returned to pending status.")
                ->warning()
                ->send();

            // Refresh the component
            $this->dispatch('$refresh');
        }
    }

    // Remove all widgets
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getWidgets(): array
    {
        return [];
    }
}
