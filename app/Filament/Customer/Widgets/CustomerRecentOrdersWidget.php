<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class CustomerRecentOrdersWidget extends Widget
{
    protected array|string|int $columnSpan = 'full';

    public function render(): View
    {
        return view('filament.customer.widgets.customer-recent-orders-widget');
    }

    public function getRecentOrders()
    {
        return Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function getCompletedOrders()
    {
        return Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereNotNull('completion_photo')
            ->orderBy('completed_at', 'desc')
            ->take(3)
            ->get();
    }
}