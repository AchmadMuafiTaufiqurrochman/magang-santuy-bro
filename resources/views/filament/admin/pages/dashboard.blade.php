<x-filament::page>
    <!-- Welcome Card -->
    <x-filament::card class="p-6 mb-6">
        <div class="flex items-center space-x-4">
            <!-- Icon Circle -->
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-primary-600 text-white">

            </div>

            <!-- Welcome Text -->
            <div>
                <h2 class="text-xl font-bold">
                    Selamat Datang, {{ auth()->user()->name }}
                </h2>
                <p class="text-gray-600">
                    Kelola layanan service AC Anda dengan mudah bersama
                    <span class="font-semibold text-primary-600">SITUKANG</span>.
                </p>
            </div>
        </div>
    </x-filament::card>
</x-filament::page>
