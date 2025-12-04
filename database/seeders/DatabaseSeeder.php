<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'technician_status' => 'offline',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Customer User',
                'email' => 'customer@gmail.com',
                'phone' => '081298765432',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'technician_status' => 'offline',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Technician User',
                'email' => 'technician@gmail.com',
                'phone' => '081277788899',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'technician_status' => 'offline',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Packages
        DB::table('packages')->insert([
            [
                'id' => 1,
                'name' => 'Paket Cuci AC',
                'price' => 150000,
                'description' => 'Paket layanan cuci AC untuk 1 unit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Paket Isi Freon',
                'price' => 250000,
                'description' => 'Isi freon AC R32 / R410A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Paket Service Lengkap',
                'price' => 500000,
                'description' => 'Cuci + cek kebocoran + isi freon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Services
        DB::table('services')->insert([
            [
                'id' => 1,
                'name' => 'Pemasangan AC Baru',
                'description' => 'Instalasi unit AC baru di rumah/kantor',
                'price' => 400000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Perbaikan AC',
                'description' => 'Service AC mati total / tidak dingin',
                'price' => 350000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Cuci AC',
                'description' => 'Membersihkan unit indoor & outdoor',
                'price' => 150000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Isi Freon AC',
                'description' => 'Pengisian freon R32/R410A',
                'price' => 250000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Products
        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => 'Remote AC Universal',
                'description' => 'Remote pengganti untuk semua merk AC',
                'price' => 50000,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Filter AC',
                'description' => 'Filter udara untuk unit AC split',
                'price' => 75000,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Kompresor AC',
                'description' => 'Sparepart kompresor AC 1/2 PK',
                'price' => 1200000,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Freon R32 3kg',
                'description' => 'Tabung freon AC R32 kapasitas 3kg',
                'price' => 600000,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Orders
        DB::table('orders')->insert([
            [
                'id' => 1,
                'user_id' => 2, // customer
                'technician_id' => 3, // teknisi
                'service_id' => 3, // Cuci AC
                'package_id' => 1, // Paket Cuci AC
                'product_id' => null,
                'service_date' => now()->subDays(2),
                'time_slot' => '10:00:00',
                'address' => 'Jl. Contoh No. 123, Surabaya',
                'notes' => 'AC kamar tidur utama',
                'status' => 'completed',
                'total_price' => 150000,
                'order_date' => now()->subDays(2),
                'completion_photo' => null,
                'completion_notes' => 'Sudah selesai dibersihkan',
                'completed_at' => now()->subDay(),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDay(),
            ],
            [
                'id' => 2,
                'user_id' => 2, // customer
                'technician_id' => 3, // teknisi
                'service_id' => 4, // Isi Freon
                'package_id' => 2, // Paket Isi Freon
                'product_id' => 4, // Freon R32
                'service_date' => now()->addDay(),
                'time_slot' => '14:00:00',
                'address' => 'Jl. Contoh No. 456, Sidoarjo',
                'notes' => 'AC ruang tamu tidak dingin',
                'status' => 'assigned',
                'total_price' => 850000, // service + produk
                'order_date' => now(),
                'completion_photo' => null,
                'completion_notes' => null,
                'completed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Order Assignments
        DB::table('order_assignments')->insert([
            [
                'id' => 1,
                'order_id' => 1,
                'technician_id' => 3,
                'assigned_by' => 1, // Admin yang assign
                'assigned_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'id' => 2,
                'order_id' => 2,
                'technician_id' => 3,
                'assigned_by' => 1, // Admin yang assign
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Transactions
        DB::table('transactions')->insert([
            [
                'id' => 1,
                'order_id' => 1,
                'amount' => 150000,
                'payment_method' => 'cash',
                'status' => 'paid',   // ✅ sesuai enum
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'order_id' => 2,
                'amount' => 850000,
                'payment_method' => 'bank_transfer',
                'status' => 'pending', // ✅ ganti "unpaid" jadi "pending"
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
