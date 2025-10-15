<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Status Toggle -->
        <div class="bg-white rounded-lg p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">👨‍🔧 Status Ketersediaan</h3>
                    <p class="text-sm text-gray-600 mt-1">Aktifkan untuk menerima tugas baru dari admin</p>
                </div>

                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium {{ $this->isAvailable ? 'text-green-600' : 'text-red-600' }}">
                        {{ $this->isAvailable ? '🟢 Online - Tersedia' : '🔴 Offline - Tidak Tersedia' }}
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

        <!-- Orders Table -->




        <div class="bg-white rounded-lg shadow-sm border">
            {{ $this->table }}
        </div>

    </div>

    <!-- Camera Modal -->
    @if($showCameraModal)
    <div class="fixed inset-0 bg-black z-50" style="z-index: 9999;">
        <div class="w-full h-full flex flex-col">
            <!-- Header -->
            <div class="bg-gray-900 p-4 flex justify-between items-center">
                <h3 class="text-white text-lg font-semibold">📷 Complete Order #{{ $selectedOrderId }}</h3>
                <button wire:click="closeCameraModal" class="text-white hover:text-gray-300 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Camera Status -->
            <div id="camera-status" class="bg-yellow-600 text-white text-center py-3 px-4 text-sm font-medium">
                📷 Initializing camera...
            </div>

            <!-- Camera Container -->
            <div class="flex-1 relative bg-black">
                @if(!$capturedPhotoData)
                    <!-- Camera Preview -->
                    <div class="w-full h-full relative">
                        <video id="camera-preview" class="w-full h-full object-cover" autoplay playsinline muted></video>
                        <canvas id="camera-canvas" class="hidden"></canvas>
                        
                        <!-- Camera overlay -->
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute inset-4 border-2 border-white border-opacity-50 rounded-lg"></div>
                        </div>
                    </div>
                @else
                    <!-- Captured Photo Preview -->
                    <div class="w-full h-full flex flex-col items-center justify-center p-4">
                        <img src="{{ $capturedPhotoData }}" class="max-w-full max-h-full object-contain rounded-lg" alt="Captured photo">
                        
                        <!-- Notes Section -->
                        <div class="w-full max-w-md mt-4">
                            <label class="block text-white text-sm font-medium mb-2">
                                📝 Catatan Penyelesaian (Opsional)
                            </label>
                            <textarea 
                                wire:model="completionNotes" 
                                rows="3" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tambahkan catatan tentang penyelesaian pekerjaan..."></textarea>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Camera Controls -->
            <div class="bg-gray-900 p-6">
                @if(!$capturedPhotoData)
                    <!-- Capture Controls -->
                    <!-- Control Buttons Row 1 -->
                    <div class="flex justify-center items-center space-x-4 mb-4">
                        <button wire:click="closeCameraModal" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm transition-colors">
                            ❌ Cancel
                        </button>
                        <button onclick="initCamera()" class="px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-xs transition-colors">
                            🔄 Retry Camera
                        </button>
                        <button onclick="switchCamera()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition-colors">
                            🔄 Switch Cam
                        </button>
                    </div>
                    
                    <!-- Main Capture Button -->
                    <div class="flex justify-center items-center">
                        <button onclick="capturePhoto()" class="w-28 h-28 bg-white rounded-full border-4 border-gray-300 hover:border-blue-500 flex items-center justify-center shadow-2xl transform hover:scale-110 transition-all duration-200 relative group">
                            <div class="w-24 h-24 bg-red-600 rounded-full flex items-center justify-center group-hover:bg-red-700 transition-colors">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                    
                    <!-- Alternative Text Button -->
                    <div class="flex justify-center mt-4">
                        <button onclick="capturePhoto()" class="px-8 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold text-lg transition-colors shadow-lg">
                            � TAKE PHOTO
                        </button>
                    </div>
                    <p class="text-gray-300 text-center text-sm mt-3">📸 Choose your capture method:</p>
                    <p class="text-gray-400 text-center text-xs mt-1">• Tap the RED CIRCLE button, OR</p>
                    <p class="text-gray-400 text-center text-xs">• Tap the "TAKE PHOTO" button</p>
                    <p class="text-gray-500 text-center text-xs mt-2">Make sure you are online to complete the order</p>
                @else
                    <!-- Photo Review Controls -->
                    <div class="flex justify-center items-center space-x-4">
                        <button wire:click="retakePhoto" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                            🔄 Retake Photo
                        </button>
                        <button wire:click="completeOrder" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors" wire:loading.attr="disabled">
                            <span wire:loading.remove>✅ Complete Order</span>
                            <span wire:loading>⏳ Processing...</span>
                        </button>
                    </div>
                    <p class="text-gray-300 text-center text-sm mt-3">✅ Photo captured successfully!</p>
                    <p class="text-gray-400 text-center text-xs mt-1">Add notes (optional) and complete the order</p>
                @endif
            </div>
        </div>
    </div>
    @endif

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

    <!-- Camera JavaScript -->
    <script>
        let currentStream = null;
        let currentCamera = 'environment'; // 'user' for front camera, 'environment' for back camera

        // Initialize camera when modal opens
        document.addEventListener('livewire:updated', function () {
            if (@json($showCameraModal) && !@json($capturedPhotoData)) {
                // Prevent body scroll when camera modal is open
                document.body.style.overflow = 'hidden';
                setTimeout(initCamera, 300);
            } else if (!@json($showCameraModal)) {
                // Restore body scroll when modal is closed
                document.body.style.overflow = '';
            }
        });

        async function initCamera() {
            const statusDiv = document.getElementById('camera-status');
            const video = document.getElementById('camera-preview');
            
            if (!video) return;

            try {
                // Show loading status
                if (statusDiv) {
                    statusDiv.classList.remove('hidden');
                    statusDiv.textContent = '📷 Requesting camera access...';
                }

                // Stop any existing stream
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                }

                // Check if getUserMedia is supported
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Camera API not supported in this browser');
                }
                
                // Try with camera constraints
                const constraints = {
                    video: {
                        facingMode: currentCamera
                    }
                };

                try {
                    currentStream = await navigator.mediaDevices.getUserMedia(constraints);
                } catch (e) {
                    // Fallback to basic video constraint
                    currentStream = await navigator.mediaDevices.getUserMedia({ video: true });
                }
                
                video.srcObject = currentStream;
                
                // Wait for video to be ready
                await new Promise((resolve, reject) => {
                    video.onloadedmetadata = () => {
                        video.play().then(() => {
                            if (statusDiv) {
                                statusDiv.classList.add('hidden');
                                statusDiv.textContent = '';
                            }
                            resolve();
                        }).catch(reject);
                    };
                    
                    video.onerror = reject;
                    setTimeout(() => reject(new Error('Video load timeout')), 10000);
                });

            } catch (error) {
                let errorMessage = 'Cannot access camera. ';
                
                if (error.name === 'NotAllowedError') {
                    errorMessage += 'Please grant camera permissions.';
                } else if (error.name === 'NotFoundError') {
                    errorMessage += 'No camera found on this device.';
                } else if (error.name === 'NotSupportedError') {
                    errorMessage += 'Camera not supported on this browser.';
                } else if (error.name === 'NotReadableError') {
                    errorMessage += 'Camera is being used by another application.';
                } else {
                    errorMessage += 'Unknown error occurred.';
                }

                if (statusDiv) {
                    statusDiv.textContent = '❌ ' + errorMessage;
                    statusDiv.classList.add('bg-red-600');
                    statusDiv.classList.remove('bg-yellow-600');
                    statusDiv.classList.remove('hidden');
                }
            }
        }

        function capturePhoto() {
            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('camera-canvas');
            
            if (!video || !canvas) {
                alert('Camera elements not found. Please try again.');
                return;
            }

            if (!video.videoWidth || !video.videoHeight) {
                alert('Camera not ready. Please wait for camera to load completely.');
                return;
            }

            try {
                const context = canvas.getContext('2d');

                // Set canvas dimensions to match video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Create flash effect
                const flashDiv = document.createElement('div');
                flashDiv.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: white;
                    z-index: 99999;
                    opacity: 0.8;
                    pointer-events: none;
                `;
                document.body.appendChild(flashDiv);
                
                // Remove flash after short delay
                setTimeout(() => {
                    document.body.removeChild(flashDiv);
                }, 150);

                // Draw video frame to canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Get image data as base64
                const photoData = canvas.toDataURL('image/jpeg', 0.9);

                // Visual feedback
                const statusDiv = document.getElementById('camera-status');
                if (statusDiv) {
                    statusDiv.textContent = '📸 Photo captured! Processing...';
                    statusDiv.classList.remove('hidden', 'bg-red-600');
                    statusDiv.classList.add('bg-green-600');
                }

                // Stop camera stream
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }

                // Send to Livewire
                @this.call('capturePhoto', photoData);

            } catch (error) {
                alert('Failed to capture photo: ' + error.message + '\nPlease try again.');
            }
        }

        async function switchCamera() {
            try {
                currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
                await initCamera();
            } catch (error) {
                // Try without facingMode if switching fails
                try {
                    await initCameraWithoutFacing();
                } catch (e) {
                    alert('Camera switching failed. Please try again.');
                }
            }
        }

        // Fallback function without facingMode constraint
        async function initCameraWithoutFacing() {
            const statusDiv = document.getElementById('camera-status');
            const video = document.getElementById('camera-preview');
            
            if (!video) return;

            try {
                if (statusDiv) {
                    statusDiv.classList.remove('hidden');
                    statusDiv.textContent = '📷 Trying default camera...';
                }

                // Stop existing stream
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                }

                // Try with basic video constraint only
                const constraints = { video: true };

                currentStream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = currentStream;
                
                await new Promise((resolve, reject) => {
                    video.onloadedmetadata = () => {
                        video.play().then(() => {
                            if (statusDiv) {
                                statusDiv.classList.add('hidden');
                            }
                            resolve();
                        }).catch(reject);
                    };
                    video.onerror = reject;
                    setTimeout(() => reject(new Error('Timeout')), 10000);
                });

            } catch (error) {
                throw error;
            }
        }

        // Clean up when modal closes
        document.addEventListener('livewire:updated', function () {
            if (!@json($showCameraModal) && currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</x-filament-panels::page>
