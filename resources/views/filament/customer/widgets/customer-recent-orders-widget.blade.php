<div class="filament-widget">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">📋 Recent Orders</h3>
                <a href="{{ \App\Filament\Customer\Resources\OrderResource::getUrl('index') }}" 
                   class="text-sm text-blue-600 hover:text-blue-800">View All →</a>
            </div>
            
            @if($this->getRecentOrders()->count() > 0)
                <div class="space-y-3">
                    @foreach($this->getRecentOrders() as $order)
                    @php
                        $status = $order->status ?? 'pending';
                        $borderColor = $status === 'completed' ? 'green' : ($status === 'in_progress' ? 'blue' : 'yellow');
                        $bgColor = $status === 'completed' ? 'green' : ($status === 'in_progress' ? 'blue' : 'yellow');
                        $textColor = $status === 'completed' ? 'green' : ($status === 'in_progress' ? 'blue' : 'yellow');
                        $statusText = $status === 'completed' ? '✅ Completed' : ($status === 'in_progress' ? '🔄 In Progress' : '⏳ ' . ucfirst($status));
                    @endphp
                    <div class="border-l-4 border-{{ $borderColor }}-400 pl-4 py-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900">Order #{{ $order->id }}</h4>
                                <p class="text-sm text-gray-600">{{ $order->package->name ?? 'Custom Service' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->service_date ? $order->service_date->format('d M Y') : 'N/A' }} • {{ $order->time_slot ? $order->time_slot->format('H:i') : 'N/A' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                bg-{{ $bgColor }}-100 
                                text-{{ $textColor }}-800">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No orders yet</p>
                    <a href="{{ \App\Filament\Customer\Resources\OrderResource::getUrl('create') }}" 
                       class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Create First Order
                    </a>
                </div>
            @endif
        </div>

        <!-- Completed Orders with Photos -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">📸 Completed Orders</h3>
                <span class="text-sm text-gray-500">With Photo Evidence</span>
            </div>
            
            @if($this->getCompletedOrders()->count() > 0)
                <div class="space-y-4">
                    @foreach($this->getCompletedOrders() as $order)
                    <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                        <div class="flex items-start space-x-3">
                            <!-- Photo thumbnail -->
                            @if($order->completion_photo)
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $order->completion_photo) }}" 
                                     alt="Completion photo" 
                                     class="w-16 h-16 rounded-lg object-cover border-2 border-green-300">
                            </div>
                            @endif
                            
                            <!-- Order info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Order #{{ $order->id }}</h4>
                                        <p class="text-sm text-gray-600">{{ $order->package->name ?? 'Custom Service' }}</p>
                                        <p class="text-xs text-green-600">✅ Completed {{ $order->completed_at->diffForHumans() }}</p>
                                        @if($order->completion_notes)
                                            <p class="text-xs text-gray-500 mt-1">📝 {{ Str::limit($order->completion_notes, 60) }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ \App\Filament\Customer\Resources\OrderResource::getUrl('view', ['record' => $order]) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800 font-medium">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No completed orders with photos yet</p>
                </div>
            @endif
        </div>
        
    </div>
</div>