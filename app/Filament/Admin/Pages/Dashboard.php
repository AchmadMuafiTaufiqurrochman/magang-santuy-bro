<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Filament\Admin\Widgets\UserStats;

class Dashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'filament.admin.pages.dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class,
        ];
    }
}
