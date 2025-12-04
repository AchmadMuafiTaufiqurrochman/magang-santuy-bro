<div class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-indigo-200">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Order #{{ $assignment->order->id }} Details
            </h2>
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                {{ $assignment->order->status === 'completed' ? 'bg-green-100 text-green-800' :
                   ($assignment->order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                   ($assignment->order->status === 'assigned' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                {{ ucfirst(str_replace('_', ' ', $assignment->order->status)) }}
            </span>
        </div>

        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    👤 Informasi Customer
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-700">Nama:</span>
                        <span class="text-gray-900">{{ $assignment->order->user->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-700">📧 Email:</span>
                        <span class="text-gray-900">{{ $assignment->order->user->email }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-700">📞 Phone:</span>
                        <span class="text-gray-900">{{ $assignment->order->user->phone ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-4 border border-blue-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    🔧 Informasi Layanan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Service:</span>
                        <span class="text-gray-900 ml-2">{{ $assignment->order->package->name ?? ($assignment->order->service->name ?? 'Custom Service') }}</span>
                    </div>
                    @if ($assignment->order->package)
                        <div>
                            <span class="font-medium text-gray-700">💰 Harga:</span>
                            <span class="text-green-600 font-semibold ml-2">Rp {{ number_format($assignment->order->package->price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="font-medium text-gray-700">📅 Tanggal:</span>
                        <span class="text-gray-900 ml-2">{{ $assignment->order->service_date ? \Carbon\Carbon::parse($assignment->order->service_date)->format('d F Y') : 'Not set' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">⏰ Waktu:</span>
                        <span class="text-gray-900 ml-2">{{ $assignment->order->time_slot ? \Carbon\Carbon::parse($assignment->order->time_slot)->format('H:i') : 'Not set' }}</span>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    📍 Alamat Layanan
                </h3>
                <p class="text-gray-900">{{ $assignment->order->address }}</p>
            </div>

            <!-- Customer Notes -->
            @if ($assignment->order->notes)
                @php
                    $notes = $assignment->order->notes ?? '';
                    $cleanNote = preg_replace('/PRODUCTS:\[.*?\]\s*/i', '', $notes);
                    $cleanNote = trim($cleanNote);
                @endphp
                @if($cleanNote)
                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-lg p-4 border border-yellow-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            📝 Catatan Customer
                        </h3>
                        <p class="text-gray-900 leading-relaxed">{{ $cleanNote }}</p>
                    </div>
                @endif
            @endif

            <!-- Assignment Info -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    📋 Informasi Assignment
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Assigned At:</span>
                        <span class="text-gray-900 ml-2">{{ $assignment->assigned_at->format('d F Y H:i') }}</span>
                    </div>
                    @if ($assignment->order->completed_at)
                        <div>
                            <span class="font-medium text-gray-700">✅ Completed At:</span>
                            <span class="text-gray-900 ml-2">{{ $assignment->order->completed_at->format('d F Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Completion Details (if completed) -->
            @if ($assignment->order->status === 'completed')
                <div class="bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg p-4 border-2 border-green-300">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        ✅ Hasil Penyelesaian
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if ($assignment->order->completion_photo)
                            <div>
                                <span class="font-medium text-gray-700 block mb-2">📷 Foto Bukti:</span>
                                @php
                                    $photoPath = $assignment->order->completion_photo;
                                    $fullPhotoUrl = asset('storage/' . $photoPath);
                                @endphp
                                <div class="relative group cursor-pointer" onclick="enlargePhoto('{{ $fullPhotoUrl }}')">
                                    <img src="{{ $fullPhotoUrl }}"
                                        alt="Completion photo for Order #{{ $assignment->order->id }}"
                                        class="w-full rounded-lg border-2 border-green-300 shadow-lg hover:shadow-xl transition-all duration-200 group-hover:scale-105"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div style="display: none;" class="w-full h-48 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <div class="text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-sm">Photo not available</p>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mt-2 text-center">Klik untuk memperbesar</p>
                            </div>
                        @endif

                        @if ($assignment->order->completion_notes)
                            <div>
                                <span class="font-medium text-gray-700 block mb-2">🗒️ Catatan Penyelesaian:</span>
                                <div class="bg-white rounded-lg p-4 border border-green-200">
                                    <p class="text-gray-900 leading-relaxed">{{ $assignment->order->completion_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Photo Enlargement Modal -->
<div id="photo-enlargement-modal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 p-4"
    onclick="closePhotoModal()" style="display: none; align-items: center; justify-content: center;">
    <div class="relative max-w-4xl max-h-full">
        <img id="enlarged-photo" src="" alt="Enlarged completion photo"
            class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closePhotoModal()"
            class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="absolute bottom-4 left-4 right-4 text-center">
            <p class="text-white text-sm bg-black bg-opacity-50 rounded px-3 py-1">Click anywhere to close</p>
        </div>
    </div>
</div>

<script>
    // Simple error handling - just show placeholder if image fails
    function handleImageError(img) {
        img.onerror = null; // Prevent infinite loop
        img.style.display = 'none';

        // Create error placeholder
        const errorDiv = document.createElement('div');
        errorDiv.className =
            'w-full max-w-sm h-48 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center';
        errorDiv.innerHTML = `
        <div class="text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-sm">Photo not available</p>
        </div>
    `;

        img.parentNode.insertBefore(errorDiv, img);
    }

    function enlargePhoto(photoUrl) {
        const modal = document.getElementById('photo-enlargement-modal');
        const enlargedPhoto = document.getElementById('enlarged-photo');

        enlargedPhoto.src = photoUrl;
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePhotoModal() {
        const modal = document.getElementById('photo-enlargement-modal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePhotoModal();
        }
    });
</script>
