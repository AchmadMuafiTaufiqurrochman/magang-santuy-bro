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
                <div class="hidden md:block">
                    <svg class="w-16 h-16 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ \App\Filament\Customer\Resources\OrderResource::getUrl('create') }}"
               class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">New Order</h3>
                        <p class="text-gray-600">Book a new service</p>
                    </div>
                </div>
            </a>

            <a href="{{ \App\Filament\Customer\Resources\OrderResource::getUrl('index') }}"
               class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">My Orders</h3>
                        <p class="text-gray-600">View all orders</p>
                    </div>
                </div>
            </a>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Support</h3>
                        <p class="text-gray-600">Get help & support</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        @php
            $recentOrders = auth()->user()?->orders()->with(['package'])->latest()->take(5)->get() ?? collect();
        @endphp

        @if($recentOrders->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                    <a href="{{ \App\Filament\Customer\Resources\OrderResource::getUrl('index') }}"
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all →
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                        @continue(!$order->package) {{-- skip jika package null --}}
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $order->package->name ?? 'Unknown Package' }}</p>
                                <p class="text-sm text-gray-600">{{ $order->date?->format('d M Y') ?? '-' }} at {{ $order->time_slot?->format('H:i') ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'assigned') bg-blue-100 text-blue-800
                                @elseif($order->status === 'in_progress') bg-purple-100 text-purple-800
                                @elseif($order->status === 'done') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status ?? 'unknown')) }}
                            </span>
                            <p class="text-sm text-gray-600 mt-1">Rp {{ $order->package?->price ? number_format($order->package->price, 0, ',', '.') : '-' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
