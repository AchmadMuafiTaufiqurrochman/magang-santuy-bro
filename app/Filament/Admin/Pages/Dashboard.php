<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use BackedEnum;
use App\Filament\Admin\Widgets\UserStats;
use App\Filament\Admin\Widgets\StatsOverview;

class Dashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $slug = 'dashboard';
    protected static ?int $navigationSort = -1;
    protected string $view = 'filament.admin.pages.dashboard';


    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected function getfooterWidgets(): array
    {
        return [
            StatsOverview::class,
            UserStats::class,
        ];
    }
}

