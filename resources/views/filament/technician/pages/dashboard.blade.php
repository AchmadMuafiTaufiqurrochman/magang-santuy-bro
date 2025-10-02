<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">

        <!-- Status Toggle -->
        <div class="bg-white rounded-lg p-6 shadow-sm border mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Status Ketersediaan</h3>
                    <p class="text-sm text-gray-600 mt-1">Aktifkan untuk menerima tugas baru</p>
                </div>

                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium {{ $this->isAvailable ? 'text-green-600' : 'text-red-600' }}">
                        {{ $this->isAvailable ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                    <label class="relative inline-flex cursor-pointer">
                        <input type="checkbox" class="sr-only peer" {{ $this->isAvailable ? 'checked' : '' }}
                            wire:click="toggleAvailability">
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-green-600 relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:h-5 after:w-5 after:rounded-full after:transition-all peer-checked:after:translate-x-full">
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- New Assignment Notifications -->
        @if($this->newAssignments->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
            <div class="flex items-center mb-3">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        🔔 Order Baru Ditugaskan ({{ $this->newAssignments->count() }})
                    </h3>
                </div>
            </div>
            
            <div class="space-y-3">
                @foreach($this->newAssignments as $assignment)
                <div class="bg-white p-4 rounded-lg border border-yellow-200">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">Order #{{ $assignment->order->id }}</h4>
                            <p class="text-sm text-gray-600">Customer: {{ $assignment->order->user->name }}</p>
                            <p class="text-sm text-gray-600">Service: {{ $assignment->order->package->name ?? 'Custom Service' }}</p>
                            <p class="text-sm text-gray-500">Assigned: {{ $assignment->assigned_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                wire:click="acceptOrder({{ $assignment->id }})"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm font-medium">
                                ✅ Accept
                            </button>
                            <button 
                                wire:click="rejectOrder({{ $assignment->id }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium">
                                ❌ Reject
                            </button>
                        </div>
                    </div>
                    
                    <div class="text-xs text-gray-500 border-t pt-2">
                        <p><strong>Date:</strong> {{ $assignment->order->date->format('d M Y') }}</p>
                        <p><strong>Time:</strong> {{ $assignment->order->time_slot->format('H:i') }}</p>
                        <p><strong>Address:</strong> {{ Str::limit($assignment->order->address, 50) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Current Active Orders -->
        @if($this->pendingOrders->count() > 0)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
            <div class="flex items-center mb-3">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        📋 Order Aktif ({{ $this->pendingOrders->count() }})
                    </h3>
                </div>
            </div>
            
            <div class="grid gap-3 md:grid-cols-2">
                @foreach($this->pendingOrders as $assignment)
                <div class="bg-white p-3 rounded-lg border border-blue-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-900">Order #{{ $assignment->order->id }}</h4>
                            <p class="text-sm text-gray-600">{{ $assignment->order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $assignment->order->package->name ?? 'Custom Service' }}</p>
                        </div>
                        <span class="bg-{{ $assignment->order->status === 'assigned' ? 'yellow' : 'blue' }}-100 text-{{ $assignment->order->status === 'assigned' ? 'yellow' : 'blue' }}-800 px-2 py-1 rounded-full text-xs">
                            {{ $assignment->order->status === 'assigned' ? '📝 Assigned' : '🔄 In Progress' }}
                        </span>
                    </div>
                    
                    <div class="text-xs text-gray-500 mt-2 border-t pt-2">
                        <p>📅 {{ $assignment->order->date->format('d M Y') }} • {{ $assignment->order->time_slot->format('H:i') }}</p>
                        <p>📍 {{ Str::limit($assignment->order->address, 40) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Menu Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Jadwal Button -->
            <button
                class="bg-blue-500 hover:bg-blue-600 text-white p-8 rounded-lg shadow-sm transition-colors duration-200 text-left">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Jadwal</h3>
                        <p class="text-blue-100 text-sm">Lihat jadwal tugas hari ini</p>
                        <div class="text-2xl font-bold mt-2">{{ $this->todayOrders->count() }}</div>
                        <div class="text-blue-200 text-xs">Tugas hari ini</div>
                    </div>
                </div>
            </button>

            <!-- Report Button -->
            <button
                class="bg-green-500 hover:bg-green-600 text-white p-8 rounded-lg shadow-sm transition-colors duration-200 text-left">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Report</h3>
                        <p class="text-green-100 text-sm">Lihat laporan aktivitas</p>
                        <div class="text-2xl font-bold mt-2">{{ $this->completedToday }}</div>
                        <div class="text-green-200 text-xs">Selesai hari ini</div>
                    </div>
                </div>
            </button>

            <!-- Tasks This Week -->
            <button
                class="bg-purple-500 hover:bg-purple-600 text-white p-8 rounded-lg shadow-sm transition-colors duration-200 text-left">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Minggu Ini</h3>
                        <p class="text-purple-100 text-sm">Total tugas minggu ini</p>
                        <div class="text-2xl font-bold mt-2">{{ $this->weekCount }}</div>
                        <div class="text-purple-200 text-xs">Tugas minggu ini</div>
                    </div>
                </div>
            </button>

            <!-- Tasks This Month -->
            <button
                class="bg-orange-500 hover:bg-orange-600 text-white p-8 rounded-lg shadow-sm transition-colors duration-200 text-left">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Bulan Ini</h3>
                        <p class="text-orange-100 text-sm">Total tugas bulan ini</p>
                        <div class="text-2xl font-bold mt-2">{{ $this->monthCount }}</div>
                        <div class="text-orange-200 text-xs">Tugas bulan ini</div>
                    </div>
                </div>
            </button>

        </div>

    </div>

    <!-- Loading State -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-gray-700">Updating status...</span>
        </div>
    </div>
</x-filament-panels::page>
