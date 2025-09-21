<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Paket;
use App\Models\Order;
use App\Models\Transaction;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Admin User ===
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // cek berdasarkan email
            [
                'name' => 'Admin User',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
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

        // === Technician User ===
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

        // === Seed Pakets (Package) ===
        $paket1 = Package::firstOrCreate(
            ['name' => 'Paket Basic'],
            [
                'description' => 'Paket layanan basic',
                'price' => 1000000.00, // isi default harga
            ]
        );

        $paket2 = Package::firstOrCreate(
            ['name' => 'Paket Standard'],
            [
                'description' => 'Paket layanan standard',
                'price' => 2000000.00,
            ]
        );

        $paket3 = Package::firstOrCreate(
            ['name' => 'Paket Premium'],
            [
                'description' => 'Paket layanan premium',
                'price' => 3000000.00,
            ]
        );

        // === Seed Products ===
        Product::firstOrCreate(
            ['name' => 'Paket Renovasi Dapur'],
            [
                'description' => 'Layanan renovasi dapur sederhana dengan material standar.',
                'price' => 2500000.00,
                'id_package' => $paket1->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Paket Perbaikan Atap'],
            [
                'description' => 'Perbaikan atap bocor dan pergantian genteng rusak.',
                'price' => 1500000.00,
                'id_package' => $paket2->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Paket Pengecatan Rumah'],
            [
                'description' => 'Jasa cat rumah untuk interior & eksterior.',
                'price' => 3500000.00,
                'id_package' => $paket3->id,
            ]
        );

        // === User Tambahan ===
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'phone' => '08123456789', // <-- wajib isi karena tabel butuh
            ]
        );

        // === Create Products ===
        $products = [
            [
                'name' => 'Computer Repair',
                'description' => 'Professional computer repair and maintenance services',
                'base_price' => 100000,
                'status' => 'active',
            ],
            [
                'name' => 'Mobile Phone Service',
                'description' => 'Mobile phone repair and troubleshooting services',
                'base_price' => 50000,
                'status' => 'active',
            ],
            [
                'name' => 'Network Installation',
                'description' => 'Network setup and configuration services',
                'base_price' => 200000,
                'status' => 'active',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['name' => $productData['name']],
                $productData
            );

            // Create packages for each product
            $packages = [
                [
                    'name' => $product->name . ' - Basic',
                    'price' => $product->base_price,
                    'description' => 'Basic ' . strtolower($product->name) . ' service package',
                ],
                [
                    'name' => $product->name . ' - Premium',
                    'price' => $product->base_price * 1.5,
                    'description' => 'Premium ' . strtolower($product->name) . ' service package with additional features',
                ],
                [
                    'name' => $product->name . ' - Enterprise',
                    'price' => $product->base_price * 2,
                    'description' => 'Enterprise ' . strtolower($product->name) . ' service package with full support',
                ],
            ];

            foreach ($packages as $packageData) {
                $packageData['product_id'] = $product->id;
                Paket::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'name' => $packageData['name']
                    ],
                    $packageData
                );
            }
        }

        // === Create Sample Orders ===
        $pakets = Paket::all();
        $statuses = ['pending', 'assigned', 'in_progress', 'done', 'cancelled'];

        foreach ($pakets->take(8) as $index => $paket) {
            $order = Order::firstOrCreate([
                'user_id' => $customer->id,
                'paket_id' => $paket->id,
                'date' => now()->addDays($index + 1)->format('Y-m-d'),
            ], [
                'time_slot' => '09:00:00',
                'address' => 'Jl. Testing No. ' . ($index + 1) . ', Jakarta Selatan',
                'note' => 'Sample order note for testing #' . ($index + 1),
                'status' => $statuses[array_rand($statuses)],
            ]);

            // Create transaction for the order
            Transaction::firstOrCreate([
                'order_id' => $order->id,
            ], [
                'payment_method' => ['COD', 'transfer'][array_rand(['COD', 'transfer'])],
                'amount' => $paket->price,
                'status' => ['pending', 'paid', 'failed'][array_rand(['pending', 'paid', 'failed'])],
            ]);
        }

        // === Tambahan Dummy Users (unik email & phone) ===
        // User::factory(10)->create();
    }
}
