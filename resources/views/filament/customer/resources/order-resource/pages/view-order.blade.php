<div>
    {{-- Header dengan tombol kembali --}}
    <div class="fi-header flex flex-col gap-y-6 sm:flex-row sm:items-end sm:justify-between mb-6">
        <div class="flex items-center gap-x-4">
            <a href="{{ App\Filament\Customer\Resources\OrderResource::getUrl('index') }}"
               class="inline-flex items-center gap-x-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Orders
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order #{{ $record->id }}</h1>
        </div>
    </div>

    <div class="space-y-8">
            {{-- Order Information --}}
            <div class="border border-gray-200 rounded-lg p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Order Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Order ID</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">#{{ $record->id }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Service Package</label>
                            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $record->package->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Date & Time</label>
                            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                {{ $record->date ? $record->date->format('d F Y') : 'N/A' }}
                                @if($record->time_slot)
                                    at {{ \Carbon\Carbon::parse($record->time_slot)->format('H:i') }}
                                @endif
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Status</label>
                            <div class="mt-1">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg-yellow-100 text-yellow-800 border-yellow-200', 'Pending'],
                                        'assigned' => ['bg-blue-100 text-blue-800 border-blue-200', 'Assigned'],
                                        'in_progress' => ['bg-purple-100 text-purple-800 border-purple-200', 'In Progress'],
                                        'done' => ['bg-green-100 text-green-800 border-green-200', 'Completed'],
                                        'cancelled' => ['bg-red-100 text-red-800 border-red-200', 'Cancelled'],
                                    ];
                                    $status = $record->status ?? 'unknown';
                                    $config = $statusConfig[$status] ?? ['bg-gray-100 text-gray-800 border-gray-200', 'Unknown'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $config[0] }}">
                                    {{ $config[1] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Details --}}
            <div class="border border-gray-200 rounded-lg p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Service Details
                </h3>
                <div class="space-y-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Service Address</label>
                        <p class="mt-2 text-base text-gray-900 dark:text-white leading-relaxed">{{ $record->address ?? 'N/A' }}</p>
                    </div>

                    @if($record->note)
                    <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Additional Notes</label>
                        <p class="mt-2 text-base text-gray-900 dark:text-white leading-relaxed">{{ $record->note }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Payment Information --}}
            <div class="border border-gray-200 rounded-lg p-6 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Payment Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Service Price</label>
                        <p class="mt-1 text-2xl font-bold text-green-600">
                            Rp {{ $record->package && $record->package->price ? number_format($record->package->price, 0, ',', '.') : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm dark:bg-gray-800">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Payment Method</label>
                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                            {{ $record->transaction && $record->transaction->payment_method ? strtoupper($record->transaction->payment_method) : 'Not Set' }}
                        </p>
                    </div>

                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">Payment Status</label>
                        <div class="mt-1">
                            @php
                                $paymentStatus = $record->transaction?->status ?? null;
                                $paymentConfig = [
                                    'pending' => ['bg-yellow-100 text-yellow-800 border-yellow-200', 'Pending Payment'],
                                    'paid' => ['bg-green-100 text-green-800 border-green-200', 'Payment Completed'],
                                    'failed' => ['bg-red-100 text-red-800 border-red-200', 'Payment Failed'],
                                    'cancelled' => ['bg-gray-100 text-gray-800 border-gray-200', 'Payment Cancelled'],
                                ];
                                $paymentConf = $paymentConfig[$paymentStatus] ?? ['bg-gray-100 text-gray-800 border-gray-200', 'No Payment'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $paymentConf[0] }}">
                                {{ $paymentConf[1] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ App\Filament\Customer\Resources\OrderResource::getUrl('index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Orders
                </a>

                @if(in_array($record->status, ['pending']))
                <div class="flex space-x-3">
                    <a href="{{ App\Filament\Customer\Resources\OrderResource::getUrl('edit', ['record' => $record]) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Order
                    </a>
                </div>
                @endif
            </div>
    </div>
</div>
