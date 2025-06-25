<x-filament-panels::page>
    <x-filament::card class="max-w-md mx-auto">
        <div class="text-center" x-data @qr-scanned.window="Livewire.dispatch('processQR', { data: $event.detail });">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Scan Absensi Siswa</h2>
                <p class="text-gray-600">Arahkan kamera ke QR code siswa</p>
            </div>

            <!-- Camera container with same style as guru version -->
            <div class="relative bg-gray-100 rounded-lg overflow-hidden shadow-md mx-auto" style="width: 100%; max-width: 320px; aspect-ratio: 1/1;">
                <!-- Camera view -->
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

            <!-- Additional information -->
            <div class="mt-6 text-sm text-gray-500 px-4">
                <p>Pastikan QR code berada dalam area kotak panduan</p>
                <p class="mt-1">Format QR harus sesuai dengan standar sistem</p>
            </div>
        </div>
    </x-filament::card>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let html5QrCode;
        let isProcessing = false;
        let cameraIsRunning = false;
        const SCAN_COOLDOWN = 2000; // 2 detik sebelum refresh

        document.addEventListener("DOMContentLoaded", function() {
            const reader = document.getElementById('reader');
            const scanOverlay = document.getElementById('scan-overlay');
            const fileInput = document.getElementById('file-input');
            const fileError = document.getElementById('file-error');

            // Start camera automatically
            startCamera();

            async function startCamera() {
                if (cameraIsRunning) return;

                try {
                    // Stop camera if already running
                    if (html5QrCode && html5QrCode.isScanning) {
                        await html5QrCode.stop();
                    }

                    html5QrCode = new Html5Qrcode("reader");

                    const config = {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        },
                        aspectRatio: 1.0,
                        disableFlip: true
                    };

                    // Try environment camera first, then user camera
                    await html5QrCode.start({
                            facingMode: "environment"
                        },
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).catch(async () => {
                        await html5QrCode.start({
                                facingMode: "user"
                            },
                            config,
                            onScanSuccess,
                            onScanFailure
                        );
                    });

                    cameraIsRunning = true;
                } catch (err) {
                    console.error("Camera access error:", err);
                    showError("Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.");

                    // Retry after 3 seconds
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
                    if (!decodedData?.siswa_id || !decodedData?.timestamp) {
                        throw new Error("Format QR tidak valid untuk siswa");
                    }

                    isProcessing = true;
                    scanOverlay.classList.remove('hidden');

                    // Kirim event ke Livewire
                    window.dispatchEvent(new CustomEvent('qr-scanned', {
                        detail: decodedText
                    }));

                    // Auto-reload setelah delay
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

            // File upload handler
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (isProcessing) {
                    showError('Sedang memproses scan sebelumnya');
                    fileInput.value = '';
                    return;
                }

                if (!file.type.match('image.*')) {
                    fileError.textContent = 'Hanya file gambar yang diperbolehkan (JPG, PNG)';
                    fileError.classList.remove('hidden');
                    return;
                }

                fileInput.value = '';
                fileError.classList.add('hidden');
                isProcessing = true;
                scanOverlay.classList.remove('hidden');

                const fileScanner = new Html5Qrcode();

                fileScanner.scanFile(file, true)
                    .then(decodedText => {
                        try {
                            const decodedData = JSON.parse(decodedText);
                            if (!decodedData.siswa_id || !decodedData.timestamp) {
                                throw new Error("Format QR tidak valid");
                            }

                            window.dispatchEvent(new CustomEvent('qr-scanned', {
                                detail: decodedText
                            }));

                            // Auto-reload setelah delay
                            setTimeout(() => {
                                window.location.reload();
                            }, SCAN_COOLDOWN);
                        } catch (e) {
                            throw e;
                        }
                    })
                    .catch(err => {
                        console.error("Error scanning file:", err);
                        fileError.textContent = 'Gagal memproses QR: ' +
                            (err.message || 'Format tidak dikenali');
                        fileError.classList.remove('hidden');
                    })
                    .finally(() => {
                        scanOverlay.classList.add('hidden');
                        isProcessing = false;
                    });
            });

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