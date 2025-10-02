<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use App\Models\Schedule;

class CalendarWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.calendar-widget';

    protected int | string | array $columnSpan = 'full'; // supaya full width

    public function getSchedules()
{
    return \App\Models\Schedule::with('technician') // kalau mau load nama teknisi juga
        ->get()
        ->map(function ($schedule) {
            return [
                'id'    => $schedule->id,
                'title' => 'Order #' . $schedule->order_id . ' - ' . ucfirst($schedule->status),
                'start' => $schedule->scheduled_date . ' ' . $schedule->scheduled_time,
                'end'   => null, // kalau belum ada durasi
            ];
        });
    }
}
