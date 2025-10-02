<x-filament-panels::page>
    <div class="grid gap-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">
                        Welcome back, {{ auth()->user()?->name ?? 'Guest' }}!
                    </h2>
                    <p class="text-blue-100 mt-1">Here's what's happening with your orders today.</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
