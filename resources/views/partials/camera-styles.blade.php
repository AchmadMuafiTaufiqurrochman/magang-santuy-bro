@php
    $isShowingCamera = $showCameraModal ?? false;
@endphp

@if($isShowingCamera)
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        /* Full screen camera styles */
        body.camera-active {
            overflow: hidden !important;
        }
        
        .camera-modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 99999 !important;
            background: #000 !important;
        }
        
        #camera-preview {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        
        /* Mobile specific styles */
        @media (max-width: 768px) {
            .camera-controls {
                padding: 20px 15px !important;
            }
            
            .capture-button {
                width: 70px !important;
                height: 70px !important;
            }
            
            .capture-button-inner {
                width: 58px !important;
                height: 58px !important;
            }
        }
        
        /* Landscape orientation */
        @media (orientation: landscape) and (max-height: 600px) {
            .camera-header {
                padding: 8px 16px !important;
            }
            
            .camera-controls {
                padding: 12px 20px !important;
            }
        }
    </style>
@endif