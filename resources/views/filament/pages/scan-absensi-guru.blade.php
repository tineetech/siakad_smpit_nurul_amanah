<x-filament-panels::page>
    <x-filament::card>
        <div class="text-center" x-data @qr-scanned.window="Livewire.dispatch('processQR', { data: $event.detail });">
            <h2 class="text-xl font-bold mb-4">Scan Absensi Guru</h2>

            <!-- Kamera dengan overlay loading -->
            <div id="reader" style="width:300px; height:300px; margin: 0 auto; position: relative;">
                <div id="scan-overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 10; justify-content: center; align-items: center; flex-direction: column;">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
                    <p class="text-white mt-2">Memproses QR Code...</p>
                </div>
            </div>

            <!-- Upload file gambar QR -->
            <div id="file-section" class="mt-4">
                <p class="text-sm text-gray-600 mb-2">Atau upload gambar QR Code:</p>
                <input type="file" id="file-input" accept="image/*" class="hidden">
                <label for="file-input" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded inline-block cursor-pointer">
                    Pilih Gambar
                </label>
                <div id="file-error" class="hidden p-4 bg-red-100 text-red-800 rounded-lg mt-2"></div>
            </div>

            <p class="text-sm text-gray-600 mt-4">
                Arahkan kamera ke QR code guru atau upload gambar QR code untuk absensi
            </p>
        </div>
    </x-filament::card>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let html5QrCode;
        let isProcessing = false;
        const SCAN_COOLDOWN = 3000; // 3 detik cooldown antara scan

        document.addEventListener("DOMContentLoaded", function() {
            const reader = document.getElementById('reader');
            const scanOverlay = document.getElementById('scan-overlay');
            const fileInput = document.getElementById('file-input');
            const fileError = document.getElementById('file-error');

            // Mulai kamera otomatis
            startCamera();

            async function startCamera() {
                try {
                    html5QrCode = new Html5Qrcode("reader");
                    
                    await html5QrCode.start({
                            facingMode: "environment"
                        }, {
                            fps: 10,
                            qrbox: {
                                width: 250,
                                height: 250
                            },
                            disableFlip: true
                        },
                        onScanSuccess,
                        onScanFailure
                    );
                    
                    console.log("Camera started successfully");
                } catch (err) {
                    console.error("Error starting camera:", err);
                    showError("Tidak dapat mengakses kamera: " + (err.message || err));
                    
                    // Coba lagi setelah 3 detik
                    setTimeout(startCamera, 3000);
                }
            }

            // Fungsi untuk menampilkan error
            function showError(message) {
                Notification.make()
                    .title('Error')
                    .body(message)
                    .danger()
                    .send();
            }

            // Fungsi saat scan berhasil
            function onScanSuccess(decodedText, decodedResult) {
                if (isProcessing) return;
                
                // Validate QR format before processing
                try {
                    const decodedData = JSON.parse(decodedText);
                    if (!decodedData?.type || !decodedData?.guru_id || !decodedData?.hash) {
                        throw new Error("Invalid QR format");
                    }

                    isProcessing = true;
                    scanOverlay.style.display = 'flex';
                    
                    window.dispatchEvent(new CustomEvent('qr-scanned', {
                        detail: decodedText
                    }));

                    // Set timeout untuk mencegah scan berulang terlalu cepat
                    setTimeout(() => {
                        isProcessing = false;
                    }, SCAN_COOLDOWN);

                } catch (e) {
                    showError("QR tidak valid: " + e.message);
                    console.error("QR scan error:", e, decodedText);
                }
            }

            function onScanFailure(error) {
                // Kosongkan agar tidak spam error setiap frame
            }

            // Event listener untuk upload file
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (isProcessing) {
                    showError('Sedang memproses scan sebelumnya');
                    fileInput.value = '';
                    return;
                }

                // Validasi tipe file
                if (!file.type.match('image.*')) {
                    fileError.textContent = 'File harus berupa gambar';
                    fileError.classList.remove('hidden');
                    return;
                }

                // Reset input file
                fileInput.value = '';
                fileError.classList.add('hidden');
                isProcessing = true;

                // Tampilkan overlay untuk file
                scanOverlay.style.display = 'flex';

                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }

                html5QrCode.scanFile(file, true)
                    .then(decodedText => {
                        try {
                            // Validasi format QR sebelum dikirim
                            const decodedData = JSON.parse(decodedText);
                            if (!decodedData.type || !decodedData.guru_id || !decodedData.hash) {
                                throw new Error("Format QR tidak valid");
                            }
                            window.dispatchEvent(new CustomEvent('qr-scanned', {
                                detail: decodedText
                            }));
                        } catch (e) {
                            showError("QR tidak valid");
                            scanOverlay.style.display = 'none';
                            isProcessing = false;
                        }
                    })
                    .catch(err => {
                        console.error("Error scanning file:", err);
                        fileError.textContent = 'Gagal membaca QR code: ' + (err.message || err);
                        fileError.classList.remove('hidden');
                        scanOverlay.style.display = 'none';
                        isProcessing = false;
                    });
            });

            // Event listener untuk menutup overlay ketika absensi selesai
            window.addEventListener('absensi-recorded', () => {
                scanOverlay.style.display = 'none';
            });
            
            // Event listener untuk error handling
            window.addEventListener('absensi-error', () => {
                scanOverlay.style.display = 'none';
                isProcessing = false;
            });

            // Coba restart kamera jika terputus
            window.addEventListener('focus', () => {
                if (!html5QrCode || !html5QrCode.isScanning) {
                    startCamera();
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>