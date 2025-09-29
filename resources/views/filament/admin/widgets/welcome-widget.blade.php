<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-2xl font-bold text-gray-800">
            Selamat Datang, {{ auth()->user()->name ?? 'Admin' }} 🎉
        </h2>
        <p class="text-gray-600 mt-2">
            Semoga harimu menyenangkan! Gunakan panel ini untuk mengelola sistem.
        </p>
    </x-filament::section>
</x-filament-widgets::widget>
