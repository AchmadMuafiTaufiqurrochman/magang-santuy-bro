<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;

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

        // === Package Default ===
        $package1 = Package::firstOrCreate(
            ['name' => 'Paket Basic'],
            [
                'description' => 'Paket layanan basic',
                'price' => 1000000.00,
            ]
        );

        // === Customer User ===

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

        // === Other Packages ===
        $package2 = Package::firstOrCreate(

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'phone' => '08123456789',
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


        // === Products Default ===
        Product::firstOrCreate(
            ['name' => 'Paket Renovasi Dapur'],

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

                'price' => 3500000.00,
                'id_package' => $package3->id,
            ]
        );

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
    }
}


        // === Sample Orders & Transactions ===
        $packages = Package::all();
        $statuses = ['pending', 'assigned', 'in_progress', 'done', 'cancelled'];

        foreach ($packages->take(8) as $index => $package) {
            $order = Order::firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'package_id' => $package->id,
                    'date' => now()->addDays($index + 1)->format('Y-m-d'),
                ],
                [
                    'time_slot' => '09:00:00',
                    'address' => 'Jl. Testing No. ' . ($index + 1) . ', Jakarta Selatan',
                    'note' => 'Sample order note for testing #' . ($index + 1),
                    'status' => $statuses[array_rand($statuses)],
                ]
            );

            Transaction::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => ['COD', 'transfer'][array_rand(['COD', 'transfer'])],
                    'amount' => $package->price,
                    'status' => ['pending', 'paid', 'failed'][array_rand(['pending', 'paid', 'failed'])],
                ]
            );
        }
    }
}

