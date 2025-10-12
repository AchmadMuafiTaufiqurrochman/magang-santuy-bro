<div class="p-6 text-gray-800">
    <h2 class="text-lg font-semibold mb-4">Order #{{ $assignment->order->id }} Details</h2>

    <div class="space-y-3 text-sm">

        <!-- Baris 1 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6">
            <div><span class="font-medium">👤 Name:</span> {{ $assignment->order->user->name }}</div>
            <div><span class="font-medium">📧 Email:</span> {{ $assignment->order->user->email }}</div>
            <div><span class="font-medium">📞 Phone:</span> {{ $assignment->order->user->phone ?? 'Not provided' }}</div>
        </div>

        <!-- Baris 2 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6">
            <div><span class="font-medium">🔧 Service:</span> {{ $assignment->order->package->name ?? 'Custom Service' }}
            </div>
            @if ($assignment->order->package)
                <div><span class="font-medium">💰 Price:</span> Rp
                    {{ number_format($assignment->order->package->price, 0, ',', '.') }}</div>
            @endif
            <div><span class="font-medium">📅 Date:</span> {{ $assignment->order->date->format('d F Y') }}</div>
        </div>

        <!-- Baris 3 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6">
            <div><span class="font-medium">⏰ Time:</span> {{ $assignment->order->time_slot->format('H:i') }}</div>
            <div class="md:col-span-2"><span class="font-medium">📍 Address:</span> {{ $assignment->order->address }}
            </div>
        </div>

        <!-- Baris 4 -->
        @if ($assignment->order->note)
            <div>
                <span class="font-medium">📝 Customer Notes:</span>
                <div class="mt-1 bg-yellow-50 border border-yellow-100 rounded-lg p-3 leading-relaxed">
                    {{ $assignment->order->getCleanNoteAttribute() }}
                </div>
            </div>
        @endif

        <!-- Baris 5 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6">
            <div><span class="font-medium">📋 Assigned At:</span> {{ $assignment->assigned_at->format('d F Y H:i') }}
            </div>
            <div>
                <span class="font-medium">📊 Status:</span>
                <span
                    class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    {{ $assignment->order->status === 'done'
                        ? 'bg-green-100 text-green-800'
                        : ($assignment->order->status === 'in_progress'
                            ? 'bg-blue-100 text-blue-800'
                            : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst(str_replace('_', ' ', $assignment->order->status)) }}
                </span>
            </div>
            @if ($assignment->order->completed_at)
                <div><span class="font-medium">✅ Completed At:</span>
                    {{ $assignment->order->completed_at->format('d F Y H:i') }}</div>
            @endif
        </div>

        <!-- Baris 6 -->
        @if ($assignment->order->status === 'done')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if ($assignment->order->completion_photo)
                    <div>
                        <span class="font-medium">📷 Completion Photo:</span>
                        <div class="mt-2">
                            @php
                                $photoPath = $assignment->order->completion_photo;
                                $fullPhotoUrl = asset('storage/' . $photoPath);
                            @endphp

                            <!-- Photo with click to enlarge -->
                            <div class="relative group cursor-pointer" onclick="enlargePhoto('{{ $fullPhotoUrl }}')">
                                <img src="{{ $fullPhotoUrl }}"
                                    alt="Completion photo for Order #{{ $assignment->order->id }}"
                                    class="w-full max-w-sm rounded-lg border border-gray-300 shadow-md hover:shadow-lg transition-all duration-200 group-hover:scale-105"
                                    onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRkNGQ0ZEIiBzdHJva2U9IiNFNUU3RUIiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWRhc2hhcnJheT0iNSw1Ii8+CjxjaXJjbGUgY3g9IjE1MCIgY3k9IjgwIiByPSIyMCIgZmlsbD0iI0Y4NzE3MSIvPgo8cGF0aCBkPSJNMTQyIDcySDE1OFY4OEgxNDJWNzJaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTQyIDkySDEyNlYxMDhIMTQyVjkyWiIgZmlsbD0iI0Y4NzE3MSIvPgo8cGF0aCBkPSJNMTU4IDkySDEzNFYxMDhIMTU4VjkyWiIgZmlsbD0iI0Y4NzE3MSIvPgo8dGV4dCB4PSIxNTAiIHk9IjE0MCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNkI3MjgwIj5QaG90byBub3QgZm91bmQ8L3RleHQ+Cjx0ZXh0IHg9IjE1MCIgeT0iMTYwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM5Q0E0QUYiPlBob3RvIG5vdCBmb3VuZDwvdGV4dD4KPC9zdmc+';">


                            </div>
                        </div>
                    </div>
                @endif

                @if ($assignment->order->completion_notes)
                    <div>
                        <span class="font-medium">🗒️ Completion Notes:</span>
                        <p class="mt-1">{{ $assignment->order->completion_notes }}</p>
                    </div>
                @endif
            </div>
        @endif

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
