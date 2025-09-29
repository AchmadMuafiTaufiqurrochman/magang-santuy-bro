<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected int|string|array $columnSpan = 'full'; // biar full width
    protected static ?int $sort = -1; // biar selalu di atas

    protected string $view = 'filament.admin.widgets.welcome-widget';
}
