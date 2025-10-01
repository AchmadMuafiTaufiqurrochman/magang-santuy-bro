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

        // === Other Packages ===
        $package2 = Package::firstOrCreate(
            ['name' => 'Paket Standard'],
            [
                'description' => 'Paket layanan standard',
                'price' => 2000000.00,
            ]
        );

        $package3 = Package::firstOrCreate(
            ['name' => 'Paket Premium'],
            [
                'description' => 'Paket layanan premium',
                'price' => 3000000.00,
            ]
        );

        // === Products Default ===
        Product::firstOrCreate(
            ['name' => 'Paket Renovasi Dapur'],
            [
                'description' => 'Layanan renovasi dapur sederhana dengan material standar.',
                'price' => 2500000.00,
                'id_package' => $package1->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Paket Perbaikan Atap'],
            [
                'description' => 'Perbaikan atap bocor dan pergantian genteng rusak.',
                'price' => 1500000.00,
                'id_package' => $package2->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Paket Pengecatan Rumah'],
            [
                'description' => 'Jasa cat rumah untuk interior & eksterior.',
                'price' => 3500000.00,
                'id_package' => $package3->id,
            ]
        );

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

