<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;

use App\Models\Package;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /**
             * CUSTOMER SELECTION
             */
            Select::make('user_id')
                ->label('Customer')
                ->relationship(
                    name: 'user',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn ($query) =>
                        $query->where('role', 'customer')
                              ->where('status', 'active')
                )
                ->searchable()
                ->required(),

            /**
             * TEKNISI
             */
            Select::make('technician_id')
                ->label('Technician')
                ->relationship('technician', 'name')
                ->searchable()
                ->nullable(),




            /**
             * ORDER STATUS
             */
            Select::make('status')
                ->label('Order Status')
                ->options([
                    'pending'     => 'Pending',
                    'assigned'    => 'Assigned',
                    'in_progress' => 'In Progress',
                    'done'        => 'Done',
                    'cancelled'   => 'Cancelled',
                ])
                ->default('pending')
                ->required()
                ->live(),

            /**
             * SERVICE SELECTION
             */
            Select::make('service_id')
                ->label('Service')
                ->options(fn () =>
                    Service::all()->mapWithKeys(fn ($s) =>
                        [$s->id => "{$s->name} - Rp " . number_format($s->price, 0, ',', '.')]
                    )
                )
                ->searchable()
                ->afterStateUpdated(fn ($state, $set, $get) =>
                    $set('total_price', static::calculateTotalPrice($get))
                )
                ->required(),

            /**
             * PRODUCT SELECTION
             */
            Select::make('product_id')
                ->label('Main Product')
                ->options(fn () =>
                    Product::all()->mapWithKeys(fn ($p) =>
                        [$p->id => "{$p->name} - Rp " . number_format($p->price, 0, ',', '.')]
                    )
                )
                ->searchable()
                ->afterStateUpdated(fn ($state, $set, $get) =>
                    $set('total_price', static::calculateTotalPrice($get))
                )
                ->required(),

            /**
             * PACKAGE SELECTION (Optional)
             */
            Select::make('package_id')
                ->label('Service Package (Optional)')
                ->placeholder('Select a service package...')
                ->options(fn () =>
                    Package::all()->mapWithKeys(fn ($package) =>
                        [
                            $package->id => "{$package->name} - {$package->description} - Rp " .
                                number_format($package->price, 0, ',', '.')
                        ]
                    )
                )
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn ($state, $set, $get) =>
                    $set('total_price', static::calculateTotalPrice($get))
                )
                ->helperText('Choose a service package or skip to select individual products'),

            /**
             * SERVICE DATE & TIME
             */
            DatePicker::make('service_date')
                ->label('Service Date')
                ->required()
                ->minDate(now()->addDay())
                ->maxDate(now()->addMonths(3)),

            TimePicker::make('time_slot')
                ->label('Preferred Time')
                ->required()
                ->seconds(false)
                ->minutesStep(30)
                ->default('09:00'),

            /**
             * ADDRESS
             */
            Textarea::make('address')
                ->label('Service Address')
                ->required()
                ->rows(3)
                ->maxLength(500),

            /**
             * NOTES
             */
            Textarea::make('notes')
                ->label('Additional Notes')
                ->rows(2)
                ->maxLength(255),

            /**
             * TOTAL PRICE
             */
            TextInput::make('total_price')
                ->label('Total Price')
                ->disabled()
                ->dehydrated(true)
                ->default(0)
                ->prefix('Rp')
                ->afterStateHydrated(function ($component, $get, $state) {
                    $component->state(static::calculateTotalPrice($get));
                }),
        ]);
    }

    /**
     * Hitung total harga
     */
    protected static function calculateTotalPrice($get): int
    {
        $totalPrice = 0;

        if ($packageId = $get('package_id')) {
            $totalPrice += Package::whereKey($packageId)->value('price') ?? 0;
        }

        if ($serviceId = $get('service_id')) {
            $totalPrice += Service::whereKey($serviceId)->value('price') ?? 0;
        }

        if ($productId = $get('product_id')) {
            $totalPrice += Product::whereKey($productId)->value('price') ?? 0;
        }

        return $totalPrice;
    }
}
