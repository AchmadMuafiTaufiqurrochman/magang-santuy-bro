<x-filament::widget>
    <x-filament::card class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">👋 Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="mt-1 text-sm">
                    Senang bertemu lagi. Semoga harimu menyenangkan 🌟<br>
                    Gunakan panel ini untuk mengelola sistem dengan mudah.
                </p>
            </div>
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Welcome" class="h-20 w-20">
        </div>
    </x-filament::card>
</x-filament::widget>
