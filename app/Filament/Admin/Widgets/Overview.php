<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Overview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Users', 150)
                ->description('Jumlah user baru bulan ini')
                ->color('success'),
            Stat::make('New Orders', 75)
                ->description('Jumlah pesanan baru bulan ini')
                ->color('primary'),
            Stat::make('Pending Tickets', 12)
                ->description('Jumlah tiket yang tertunda')
                ->color('warning'),
            Stat::make('Server Uptime', '99.9%')
                ->description('Waktu aktif server bulan ini')
                ->color('info'),
        ];
    }
}
