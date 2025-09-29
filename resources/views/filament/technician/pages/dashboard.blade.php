<x-filament-panels::page>
    <div class="max-w-lg mx-auto">
        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-sm border p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Technician Dashboard</h1>
                <p class="text-gray-600">Welcome, {{ auth()->user()->name }}!</p>
            </div>

            <!-- Status Card -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-700">Current Status:</span>
                </div>

                <div class="flex items-center justify-center space-x-4">
                    <!-- Status Indicator -->
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-6 h-6 rounded-full {{ $this->isAvailable ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            @if($this->isAvailable)
                                <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                            @endif
                        </div>
                        <span class="text-xl font-semibold {{ $this->isAvailable ? 'text-green-700' : 'text-red-700' }}">
                            {{ $this->isAvailable ? 'Available' : 'Offline' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Toggle Switch -->
            <div class="text-center mb-6">
                <label class="inline-flex items-center cursor-pointer">
                    <div class="relative">
                        <input
                            type="checkbox"
                            class="sr-only"
                            {{ $this->isAvailable ? 'checked' : '' }}
                            wire:click="toggleAvailability"
                        >
                        <div class="w-14 h-8 bg-gray-200 rounded-full shadow-inner transition-colors duration-300 {{ $this->isAvailable ? 'bg-green-500' : '' }}"></div>
                        <div class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full shadow transition-transform duration-300 transform {{ $this->isAvailable ? 'translate-x-6' : '' }}"></div>
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700">
                        {{ $this->isAvailable ? 'Go Offline' : 'Go Available' }}
                    </span>
                </label>
            </div>

            <!-- Info Alert -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="text-blue-800 font-medium mb-1">How it works:</p>
                        <p class="text-blue-700">When you're available, admin can assign service orders to you. Toggle off when you're not ready to take new orders.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-2 gap-4">
            <div class="bg-white rounded-lg border p-4 text-center">
                <div class="text-2xl font-bold text-orange-600">
                    @php
                        try {
                            echo auth()->user()->orders()->count();
                        } catch (\Exception $e) {
                            echo '0';
                        }
                    @endphp
                </div>
                <div class="text-sm text-gray-600">Total Orders</div>
            </div>
            <div class="bg-white rounded-lg border p-4 text-center">
                <div class="text-2xl font-bold {{ $this->isAvailable ? 'text-green-600' : 'text-red-600' }}">
                    {{ $this->isAvailable ? 'Online' : 'Offline' }}
                </div>
                <div class="text-sm text-gray-600">Status</div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Updating status...</span>
        </div>
    </div>
</x-filament-panels::page>
