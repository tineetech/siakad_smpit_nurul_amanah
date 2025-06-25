<x-filament-panels::page>
    <x-filament::card class="max-w-md mx-auto">
        <div class="text-center" x-data @qr-scanned.window="Livewire.dispatch('processQR', { data: $event.detail });">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Scan Absensi Guru</h2>
                <p class="text-gray-600">Arahkan kamera ke QR code guru</p>
            </div>

            <!-- Camera container -->
            <div class="relative bg-gray-100 rounded-lg overflow-hidden shadow-md mx-auto" style="width: 100%; max-width: 320px; aspect-ratio: 1/1;">
                <div id="reader" class="w-full h-full"></div>
                
                <!-- Loading overlay -->
                <div id="scan-overlay" class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center hidden">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500"></div>
                    <p class="text-white mt-4 font-medium">Memproses QR Code...</p>
                </div>

                <!-- Scan frame guide -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="border-4 border-primary-500 border-dashed rounded-lg" style="width: 80%; height: 80%;"></div>
                </div>
            </div>

            <div class="mt-6 text-sm text-gray-500 px-4">
                <p>Pastikan QR code berada dalam area kotak panduan</p>
                <p class="mt-1">Pencahayaan yang cukup akan mempercepat proses scan</p>
            </div>
        </div>
    </x-filament::card>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let html5QrCode;
        let isProcessing = false;
        const SCAN_COOLDOWN = 2000; // 2 detik sebelum reload

        document.addEventListener("DOMContentLoaded", function() {
            const reader = document.getElementById('reader');
            const scanOverlay = document.getElementById('scan-overlay');

            // Start camera automatically
            startCamera();

            async function startCamera() {
                try {
                    // Stop camera if already running
                    if (html5QrCode && html5QrCode.isScanning) {
                        await html5QrCode.stop();
                    }
                    
                    html5QrCode = new Html5Qrcode("reader");
                    
                    const config = {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0,
                        disableFlip: true
                    };
                    
                    // Try environment camera first, then user camera
                    await html5QrCode.start(
                        { facingMode: "environment" }, 
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).catch(async () => {
                        await html5QrCode.start(
                            { facingMode: "user" }, 
                            config,
                            onScanSuccess,
                            onScanFailure
                        );
                    });
                    
                } catch (err) {
                    console.error("Camera access error:", err);
                    showError("Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.");
                    setTimeout(startCamera, 3000);
                }
            }

            function showError(message) {
                Notification.make()
                    .title('Gagal')
                    .body(message)
                    .danger()
                    .send();
            }

            function onScanSuccess(decodedText, decodedResult) {
                if (isProcessing) return;
                
                try {
                    const decodedData = JSON.parse(decodedText);
                    if (!decodedData?.type || decodedData.type !== 'guru' || !decodedData?.guru_id || !decodedData?.hash) {
                        throw new Error("Format QR tidak valid untuk guru");
                    }

                    isProcessing = true;
                    scanOverlay.classList.remove('hidden');
                    
                    // Kirim data ke Livewire
                    window.dispatchEvent(new CustomEvent('qr-scanned', {
                        detail: decodedText
                    }));

                    // Auto reload setelah delay
                    setTimeout(() => {
                        window.location.reload();
                    }, SCAN_COOLDOWN);

                } catch (e) {
                    showError("QR tidak valid: " + e.message);
                    console.error("QR scan error:", e, decodedText);
                    scanOverlay.classList.add('hidden');
                    isProcessing = false;
                }
            }

            function onScanFailure(error) {
                console.debug("Scan error:", error);
            }

            // Cleanup camera when page unloads
            window.addEventListener('beforeunload', function() {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop();
                }
            });

            // Handle visibility changes
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && (!html5QrCode || !html5QrCode.isScanning)) {
                    startCamera();
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>