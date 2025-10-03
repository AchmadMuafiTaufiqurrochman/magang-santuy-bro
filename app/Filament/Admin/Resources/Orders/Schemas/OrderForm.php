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
        return $schema->components([

            /**
             * CUSTOMER SELECTION
             * Pilih customer dari tabel users (role = customer & status = active)
             */
            Select::make('user_id')
                ->label('Customer')
                ->relationship('user', 'name', fn ($query) =>
                    $query->where('role', 'customer')->where('status', 'active')
                )
                ->searchable()
                ->required(),

            /**
             * TEKNISI (opsional, bisa di-assign belakangan oleh admin)
             */
            Select::make('technician_id')
                ->label('Technician')
                ->relationship('technician', 'name')
                ->searchable()
                ->nullable(),

            /**
             * ORDER DATE
             * Tanggal order dibuat (biasanya otomatis saat customer submit)
             */
            DateTimePicker::make('order_date')
                ->label('Order Date')
                ->default(now())
                ->required(),

            /**
             * ORDER STATUS
             * Status utama order untuk tracking progress
             */
            Select::make('status')
                ->label('Order Status')
                ->options([
                    'pending'    => 'Pending',
                    'assigned'   => 'Assigned',
                    'in_progress'=> 'In Progress',
                    'done'       => 'Done',
                    'cancelled'  => 'Cancelled',
                ])
                ->default('pending')
                ->required()
                ->live()
                ->helperText('Current status of the order'),
            /**
             * SERVICE SELECTION
             * Pilih layanan dari tabel services
             */
            Select::make('service_id')
                ->label('Service')
                ->relationship('service', 'name')
                ->searchable()
                ->required(),

            /**PRODUCT SELECTION
             * Pilih produk dari tabel products
             */
            Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'name')
                ->searchable()
                ->required(),

            /**
             * PRODUCTS SELECTION
             * Bisa pilih beberapa produk yang diinginkan customer
             * Hasilnya akan dihitung otomatis di total price
             * Jika pilih paket, produk diabaikan
             */
            Select::make('package_id')
                ->label('Service Package (Optional)')
                ->placeholder('Select a service package...')
                ->options(fn () =>
                    Package::all()->mapWithKeys(fn ($package) =>
                        [
                            $package->id => $package->name
                                . ' - ' . $package->description // 👈 tambahkan description
                                . ' - Rp ' . number_format($package->price, 0, ',', '.')
                        ]
                    )
                )
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn ($state, $set, $get) =>
                    $set('price_calculation', static::calculateTotalPrice($get))
                )
                ->helperText('Choose a service package or skip to select individual products'),

            /**
             * SERVICE DATE & TIME
             * Jadwal permintaan layanan dari customer
             */
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

            /**
             * SERVICE ADDRESS
             * Alamat customer
             */
            Textarea::make('address')
                ->label('Service Address')
                ->required()
                ->rows(3)
                ->maxLength(500)
                ->placeholder('Please provide your complete address for service delivery...'),

            /**
             * ADDITIONAL NOTES
             * Catatan tambahan customer
             */
            Textarea::make('note')
                ->label('Additional Notes')
                ->rows(2)
                ->maxLength(255)
                ->placeholder('Any special instructions or requests...'),

            /**
             * TOTAL PRICE
             * Perhitungan otomatis dari package + products
             */
            TextInput::make('price_calculation')
                ->label('💰 Total Price')
                ->disabled()
                ->default('Rp 0')
                ->dehydrated(false) // tidak tersimpan langsung ke DB
                ->prefix('Total:')
                ->afterStateHydrated(function ($component, $get, $state) {
                    $component->state(static::calculateTotalPrice($get));
                }),

            /**
             * CURRENT TECHNICIAN INFO
             * Menampilkan teknisi yang sudah assign
             */
            TextInput::make('current_technician')
                ->label('Current Assigned Technician')
                ->disabled()
                ->dehydrated(false)
                ->formatStateUsing(function ($record) {
                    if ($record && $record->technician) {
                        return $record->technician->name;
                    }
                    return 'No technician assigned yet';
                })
                ->helperText('Currently assigned technician for this order'),

            /**
             * ASSIGN / CHANGE TECHNICIAN (Admin Only)
             * Pilih teknisi aktif, hanya muncul kalau status assigned / in_progress
             */
            Select::make('technician_assignment')
                ->label('Assign/Change Technician')
                ->placeholder('Select a technician to assign...')
                ->options(fn () =>
                    User::where('role', 'technician')
                        ->where('status', 'active')
                        ->get()
                        ->mapWithKeys(fn ($tech) =>
                            [$tech->id => $tech->name . ' (' . $tech->email . ')']
                        )
                )
                ->searchable()
                ->preload()
                ->visible(fn ($get) => in_array($get('status'), ['assigned', 'in_progress']))
                ->helperText('Assign or change technician when status is Assigned or In Progress')
                ->dehydrated(false), // tidak simpan langsung, di-handle via hook Resource
        ]);
    }

    /**
     * Function untuk menghitung total harga
     * - Jika pilih paket → ambil harga paket
     * - Jika pilih produk → sum harga produk
     * - Hasil dalam format Rp
     */
    protected static function calculateTotalPrice($get): string
    {
        $totalPrice = 0;

        // Paket
        if ($packageId = $get('package_id')) {
            $totalPrice += Package::whereKey($packageId)->value('price') ?? 0;
        }

        // Produk
        if ($selectedProducts = $get('selected_products')) {
            $totalPrice += Product::whereIn('id', $selectedProducts)->sum('price');
        }

        return $totalPrice > 0
            ? "Rp " . number_format($totalPrice, 0, ',', '.')
            : 'Rp 0';
    }
}
