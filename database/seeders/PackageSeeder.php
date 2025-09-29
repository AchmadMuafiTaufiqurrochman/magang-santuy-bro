<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Service AC Reguler',
                'description' => 'Service AC standar untuk perawatan rutin',
                'price' => 150000,
            ],
            [
                'name' => 'Service AC Premium',
                'description' => 'Service AC lengkap dengan pembersihan menyeluruh',
                'price' => 250000,
            ],
            [
                'name' => 'Instalasi AC Baru',
                'description' => 'Instalasi unit AC baru lengkap dengan pemasangan',
                'price' => 500000,
            ],
            [
                'name' => 'Perbaikan AC Rusak',
                'description' => 'Perbaikan AC yang mengalami kerusakan',
                'price' => 300000,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
