<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use App\Filament\Customer\Widgets\CustomerStatsWidget;
use App\Filament\Customer\Widgets\CustomerOrdersChart;
use BackedEnum;

class Dashboard extends Page
{
    // ✅ perbaikan type hint untuk Filament 4
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    // ✅ harus non-static di Filament 4
    protected string $view = 'filament.customer.pages.dashboard';

    protected static ?string $title = 'Dashboard Customer';

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerStatsWidget::class,
            CustomerOrdersChart::class,
        ];
    }
}
