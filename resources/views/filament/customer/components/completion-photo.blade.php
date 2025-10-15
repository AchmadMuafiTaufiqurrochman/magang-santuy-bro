@php
    $photoPath = $record->completion_photo;
    
    // Generate optimized URLs for faster loading
    $thumbnailUrl = asset('storage/' . $photoPath);
    $fullUrl = asset('storage/' . $photoPath);
@endphp

<div class="space-y-3">
    @if($photoPath)
        <!-- Fast Loading Photo Container -->
        <div class="relative group">
            <!-- Simple, fast-loading image with immediate loading -->
            <img 
                src="{{ $thumbnailUrl }}"
                alt="Photo Bukti Penyelesaian"
                class="w-full max-w-md rounded-lg border border-gray-300 shadow-md cursor-pointer hover:shadow-lg transition-shadow duration-200"
                style="height: auto; max-height: 300px; object-fit: cover; opacity: 1;"
                onclick="enlargePhoto('{{ $fullUrl }}')"
                loading="eager"
                decoding="sync"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
            >

            <!-- Error fallback (hidden by default) -->
            <div style="display: none;" class="w-full h-48 bg-red-50 border-2 border-dashed border-red-300 rounded-lg flex items-center justify-center">
                <div class="text-center text-red-500">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-sm">Photo unavailable</p>
                </div>
            </div>

            
        </div>

        <!-- Quick info -->
        <div class="text-sm text-gray-600 bg-green-50 p-2 rounded-md">
            <p class="flex items-center gap-2">
                <span class="text-green-600">✅</span> 
                Photo bukti penyelesaian tersedia
            </p>
           
        </div>
    @else
        <!-- No photo available -->
        <div class="w-full h-48 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
            <div class="text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-sm">No completion photo available</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal will be created dynamically by JavaScript for better performance -->

<script>
    function enlargePhoto(photoUrl) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('photo-enlargement-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'photo-enlargement-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4';
            modal.style.display = 'none';
            modal.onclick = closePhotoModal;
            
            const container = document.createElement('div');
            container.className = 'relative max-w-4xl max-h-full';
            
            const img = document.createElement('img');
            img.id = 'enlarged-photo';
            img.className = 'max-w-full max-h-full object-contain rounded-lg';
            img.alt = 'Enlarged completion photo';
            
            const closeBtn = document.createElement('button');
            closeBtn.onclick = closePhotoModal;
            closeBtn.className = 'absolute top-4 right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2';
            closeBtn.innerHTML = '✕';
            
            container.appendChild(img);
            container.appendChild(closeBtn);
            modal.appendChild(container);
            document.body.appendChild(modal);
        }

        const enlargedPhoto = modal.querySelector('#enlarged-photo');
        enlargedPhoto.src = photoUrl;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePhotoModal() {
        const modal = document.getElementById('photo-enlargement-modal');
        if (modal) {
            modal.style.display = 'none';
        }
        document.body.style.overflow = '';
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePhotoModal();
        }
    });
</script>

<style>
    /* Fast loading transition */
    img[onclick] {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
</style>