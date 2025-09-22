<?php

namespace Database\Seeders;


use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Paket;
use App\Models\Product;

use Illuminate\Database\Seeder;
use App\Models\User;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        /**
         * === Seed Users ===
         */
        User::firstOrCreate(
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

        /**
         * === Seed Pakets ===
         */
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
        /**
         * === Seed Products ===
         */
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

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'phone' => '08123456789', // <-- wajib isi karena tabel butuh

            ]
        );
    }
}
