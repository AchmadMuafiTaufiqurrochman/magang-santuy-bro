<?php

namespace App\Filament\Admin\Resources\Reports\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            DatePicker::make('from_date')
                ->label('From Date')
                ->placeholder('Select start date'),

            DatePicker::make('until_date')
                ->label('Until Date')
                ->placeholder('Select end date'),

            Select::make('status')
                ->label('Order Status')
                ->options([
                    'pending' => 'Pending',
                    'assigned' => 'Assigned',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
        ]);
    }
}
