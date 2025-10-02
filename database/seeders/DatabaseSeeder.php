<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\Schedule;
use App\Models\Service;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Users ===
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer User',
                'phone' => '081298765432',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $technician = User::firstOrCreate(
            ['email' => 'technician@gmail.com'],
            [
                'name' => 'Technician User',
                'phone' => '081277788899',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // === Packages ===
        $packageBasic = Package::firstOrCreate(
            ['name' => 'Paket Basic'],
            ['description' => 'Paket layanan basic', 'price' => 1000000]
        );

        $packageStandard = Package::firstOrCreate(
            ['name' => 'Paket Standard'],
            ['description' => 'Paket layanan standard', 'price' => 2000000]
        );

        $packagePremium = Package::firstOrCreate(
            ['name' => 'Paket Premium'],
            ['description' => 'Paket layanan premium', 'price' => 3000000]
        );

        // === Services (kalau kosong, buat dummy) ===
        if (Service::count() === 0) {
            Service::create([
                'name' => 'Service AC',
                'description' => 'Layanan perbaikan & pemasangan AC',
                'price' => 500000,
            ]);
        }

        // === Products ===
        $products = [
            [
                'name' => 'Paket Renovasi Dapur',
                'description' => 'Layanan renovasi dapur sederhana dengan material standar.',
                'price' => 2500000,
                'id_package' => $packageBasic->id,
            ],
            [
                'name' => 'Paket Perbaikan Atap',
                'description' => 'Perbaikan atap bocor dan pergantian genteng rusak.',
                'price' => 1500000,
                'id_package' => $packageStandard->id,
            ],
            [
                'name' => 'Paket Pengecatan Rumah',
                'description' => 'Jasa cat rumah untuk interior & eksterior.',
                'price' => 3500000,
                'id_package' => $packagePremium->id,
            ],
            [
                'name' => 'Computer Repair',
                'description' => 'Professional computer repair and maintenance services',
                'price' => 100000,
                'id_package' => $packageBasic->id,
            ],
            [
                'name' => 'Mobile Phone Service',
                'description' => 'Mobile phone repair and troubleshooting services',
                'price' => 50000,
                'id_package' => $packageBasic->id,
            ],
            [
                'name' => 'Network Installation',
                'description' => 'Network setup and configuration services',
                'price' => 200000,
                'id_package' => $packageBasic->id,
            ],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(['name' => $prod['name']], $prod);
        }

        // === Additional User ===
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'phone' => '08123456789',
            ]
        );

        // === Sample Orders ===
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $packages = Package::all();
        $services = Service::all();
        $products = Product::all();

        foreach ($packages->take(5) as $index => $package) {
            $service  = $services->isNotEmpty() ? $services->random() : Service::create([
                'name' => 'Default Service',
                'description' => 'Dummy service for testing',
                'price' => 100000,
            ]);

            $product  = $products->isNotEmpty() ? $products->random() : Product::create([
                'name' => 'Default Product',
                'description' => 'Dummy product for testing',
                'price' => 100000,
                'id_package' => $package->id,
            ]);

            $order = Order::firstOrCreate(
                [
                    'user_id'    => $customer->id,
                    'package_id' => $package->id,
                    'service_id' => $service->id,
                    'product_id' => $product->id,
                    'order_date' => now()->addDays($index + 1),
                ],
                [
                    'notes'        => 'Sample order note for testing #' . ($index + 1),
                    'status'       => $statuses[array_rand($statuses)],
                    'technician_id'=> $technician->id,
                    'total_price'  => $package->price + $product->price,
                    'time_slot'    => '09:00:00', // default slot
                ]
            );

            Transaction::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => ['COD', 'transfer'][rand(0, 1)],
                    'amount'         => $package->price + $product->price,
                    'status'         => ['pending', 'paid', 'failed'][rand(0, 2)],
                ]
            );

            // === Jadwal untuk Order (Schedule) ===
            Schedule::create([
                'order_id'       => $order->id,
                'technician_id'  => $technician->id,
                'scheduled_date' => now()->addDay()->toDateString(),
                'scheduled_time' => '10:00:00',
                'status'         => 'pending',
                'notes'          => 'Initial inspection schedule',
            ]);

            Schedule::create([
                'order_id'       => $order->id,
                'technician_id'  => $technician->id,
                'scheduled_date' => now()->addDays(2)->toDateString(),
                'scheduled_time' => '14:00:00',
                'status'         => 'confirmed',
                'notes'          => 'Confirmed schedule for service',
            ]);

            Schedule::create([
                'order_id'       => $order->id,
                'technician_id'  => $technician->id,
                'scheduled_date' => now()->subDay()->toDateString(),
                'scheduled_time' => '09:00:00',
                'status'         => 'completed',
                'notes'          => 'Service completed successfully',
            ]);
        }
    }
}
