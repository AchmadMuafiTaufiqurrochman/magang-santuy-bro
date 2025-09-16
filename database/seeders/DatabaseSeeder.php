<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // === Admin User ===
        User::firstOrCreate(
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
        User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer User',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // === Technician User ===
        User::firstOrCreate(
            ['email' => 'technician@gmail.com'],
            [
                'name' => 'Technician User',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // === Tambahan Dummy Users (unik email & phone) ===
        User::factory(10)->create();
    }
}
