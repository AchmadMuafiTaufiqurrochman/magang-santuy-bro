<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class CustomerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        return [
            BaseWidget\Stat::make('Total Orders', $user->orders()->count())
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            BaseWidget\Stat::make('Pending Orders', $user->orders()->where('status', 'pending')->count())
                ->description('Orders waiting for assignment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            BaseWidget\Stat::make('Completed Orders', $user->orders()->where('status', 'completed')->count())
                ->description('Successfully completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            BaseWidget\Stat::make(
                'Total Spent',
                'Rp ' . number_format(
                    $user->orders()->with('transaction')->get()->sum(
                        fn ($order) => $order->transaction ? $order->transaction->amount : 0
                    ),
                    0, ',', '.'
                )
            )
                ->description('Total amount spent')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
