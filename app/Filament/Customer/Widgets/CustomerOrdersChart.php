<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class CustomerOrdersChart extends ChartWidget
{
    protected ?string $heading = 'Orders This Year';

    protected function getData(): array
    {
        $user = Auth::user();
        $months = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $data[] = $user->orders()
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $i)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
