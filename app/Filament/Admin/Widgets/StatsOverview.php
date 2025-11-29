<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description("Pending: " . Order::where('status', 'pending')->count() .
                              " | Proses: " . Order::where('status', 'in_progress')->count() .
                              " | Selesai: " . Order::where('status', 'completed')->count())
                ->color('primary'),

            Stat::make('Total Transactions', Transaction::count())
                ->color('success'),

            Stat::make('User Login Today', User::whereNotNull('last_login_at')
                ->whereDate('last_login_at', now()->toDateString())
                ->count())
                ->color('warning'),
        ];
    }
}
