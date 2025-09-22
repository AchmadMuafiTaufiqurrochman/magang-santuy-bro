<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;

class UserStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Jumlah semua user')
                ->color('success'),
            Stat::make('Total Admins', User::where('role', 'admin')->count())
                ->description('Jumlah semua admin')
                ->color('primary'),
            Stat::make('Total Customers', User::where('role', 'customer')->count())
                ->description('Jumlah semua customer')
                ->color('info'),
            Stat::make('Total Technicians', User::where('role', 'technician')->count())
                ->description('Jumlah semua teknisi')
                ->color('warning'),
        ];
    }
}
