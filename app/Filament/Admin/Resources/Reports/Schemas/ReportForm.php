<?php

namespace App\Filament\Admin\Resources\Reports\Schemas;

use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Select::make('order_id')
                  ->label('Order')
                  ->relationship('order', 'id')
                  ->searchable()
                  ->required(),
            TextInput::make('title')
                  ->label('Report Title')
                  ->required()
                  ->maxLength(150),
            Textarea::make('description')
                  ->label('Description')
                  ->rows(4)
                  ->maxLength(500),
            Select::make('type')
                  ->label('Report Type')
                  ->options([
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'custom' => 'Custom',
                  ])
                  ->required(),
            TextInput::make('report_date')
                  ->label('Report Date')
                  ->type('date')
                  ->required(),
            ]);
    }
}
