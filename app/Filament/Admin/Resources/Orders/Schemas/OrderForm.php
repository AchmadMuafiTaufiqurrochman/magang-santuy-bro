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
use App\Models\User;


class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                ->label('Customer')
                ->relationship('customer', 'name')
                ->searchable()
                ->required(),

            Select::make('service_id')
                ->label('Service')
                ->relationship('service', 'name')
                ->searchable()
                ->required(),

            Select::make('technician_id')
                ->label('Technician')
                ->relationship('technician', 'name')
                ->searchable()
                ->nullable(),

            DateTimePicker::make('order_date')
                ->label('Order Date')
                ->required(),

            Select::make('status')
                ->options([
                    'pending'     => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                    'cancelled'   => 'Cancelled',
                ])
                ->default('pending')
                ->required(),

            TextInput::make('total_price')
                ->numeric()
                ->prefix('Rp')
                ->required(),

            Textarea::make('notes')
                ->label('Notes')
                ->nullable()
                ->columnSpanFull(),

                // CUSTOMER SELECTION
                Select::make('user_id')
                    ->label('Customer')
                    ->relationship('user', 'name', function ($query) {
                        return $query->where('role', 'customer')->where('status', 'active');
                    })
                    ->searchable()
                    ->required(),

                // === SERVICE SELECTION === (sama seperti customer create)
                Select::make('package_id')
                    ->label('Service Package (Optional)')
                    ->placeholder('Select a service package...')
                    ->options(function() {
                        return Package::all()->mapWithKeys(function($package) {
                            return [$package->id => $package->name . ' - Rp ' . number_format($package->price, 0, ',', '.')];
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('price_calculation', static::calculateTotalPrice($get));
                    })
                    ->helperText('Choose a service package or skip to select individual products'),

                Select::make('selected_products')
                    ->label('Individual Products (Optional)')
                    ->placeholder('Select individual products...')
                    ->options(function() {
                        return Product::all()->mapWithKeys(function($product) {
                            return [$product->id => $product->name . ' - Rp ' . number_format($product->price, 0, ',', '.')];
                        });
                    })
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('price_calculation', static::calculateTotalPrice($get));
                    })
                    ->helperText('Select multiple products - hold Ctrl/Cmd to select multiple items'),

                // === SCHEDULE & LOCATION === (sama seperti customer create)
                DatePicker::make('date')
                    ->label('Service Date')
                    ->required()
                    ->minDate(now()->addDay())
                    ->maxDate(now()->addMonths(3))
                    ->helperText('Select your preferred service date'),

                TimePicker::make('time_slot')
                    ->label('Preferred Time')
                    ->required()
                    ->seconds(false)
                    ->minutesStep(30)
                    ->default('09:00')
                    ->helperText('Choose your preferred time slot'),

                Textarea::make('address')
                    ->label('Service Address')
                    ->required()
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Please provide your complete address for service delivery...'),

                Textarea::make('note')
                    ->label('Additional Notes')
                    ->rows(2)
                    ->maxLength(255)
                    ->placeholder('Any special instructions or requests...'),

                // === ORDER SUMMARY === (sama seperti customer create)
                TextInput::make('price_calculation')
                    ->label('💰 Total Price')
                    ->disabled()
                    ->default('Rp 0')
                    ->dehydrated(false)
                    ->prefix('Total:')
                    ->afterStateHydrated(function ($component, $get, $state) {
                        // Hitung total price saat form di-load untuk edit
                        $component->state(static::calculateTotalPrice($get));
                    }),

                // === ADMIN CONTROLS ===
                // ORDER STATUS (admin control)
                Select::make('status')
                    ->label('Order Status')
                    ->options([
                        'pending' => 'Pending',
                        'assigned' => 'Assigned',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required()
                    ->live()
                    ->helperText('Current status of the order'),

                // CURRENT TECHNICIAN DISPLAY
                TextInput::make('current_technician')
                    ->label('Current Assigned Technician')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function ($record) {
                        if ($record) {
                            $assignment = $record->orderAssignments()->with('technician')->first();
                            return $assignment?->technician?->name ?? 'No technician assigned';
                        }
                        return 'New order - no assignment yet';
                    })
                    ->helperText('Currently assigned technician for this order'),

                // TECHNICIAN ASSIGNMENT
                Select::make('technician_assignment')
                    ->label('Assign/Change Technician')
                    ->placeholder('Select a technician to assign...')
                    ->options(function() {
                        return User::where('role', 'technician')
                            ->where('status', 'active')
                            ->get()
                            ->mapWithKeys(function($tech) {
                                return [$tech->id => $tech->name . ' (' . $tech->email . ')'];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => in_array($get('status'), ['assigned', 'in_progress']))
                    ->helperText('Assign or change technician when status is Assigned or In Progress')
                    ->dehydrated(false), // Tidak disimpan langsung ke order table
            ]);
    }

    protected static function calculateTotalPrice($get): string
    {
        $totalPrice = 0;

        // Hitung harga package jika dipilih
        $packageId = $get('package_id');
        if ($packageId) {
            $package = Package::find($packageId);
            if ($package) {
                $totalPrice += $package->price;
            }
        }

        // Hitung harga products jika dipilih
        $selectedProducts = $get('selected_products');
        if ($selectedProducts && is_array($selectedProducts) && count($selectedProducts) > 0) {
            $products = Product::whereIn('id', $selectedProducts)->get();
            foreach ($products as $product) {
                $totalPrice += $product->price;
            }
        }

        if ($totalPrice > 0) {
            return "Rp " . number_format($totalPrice, 0, ',', '.');
        }

        return 'Rp 0';
    }
}
