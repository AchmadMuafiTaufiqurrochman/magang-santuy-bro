<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $data = $this->getAvailabilityData();
            $status = $data['status'];
            $statusLabels = [
                'available' => 'Available',
                'busy' => 'Busy',
                'offline' => 'Offline'
            ];
        @endphp

        <div class="p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full {{ $status === 'available' ? 'bg-green-500' : ($status === 'busy' ? 'bg-yellow-500' : 'bg-gray-400') }}"></div>
                        <span class="font-medium text-gray-900">{{ $statusLabels[$status] }}</span>
                    </div>
                </div>

                <button
                    wire:click="toggleAvailability"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors
                           {{ $status === 'available' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}"
                >
                    @if($status === 'available')
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Go Offline
                    @else
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Go Available
                    @endif
                </button>
            </div>

            <p class="text-xs text-gray-500 mt-2">
                @if($status === 'available')
                    Ready to receive new assignments from admin
                @elseif($status === 'busy')
                    Currently working on assignments
                @else
                    Not available for new assignments
                @endif
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
