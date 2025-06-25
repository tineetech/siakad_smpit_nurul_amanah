<x-filament-panels::page>
    <x-filament::card>
        <div class="text-center" x-data @qr-scanned.window="Livewire.dispatch('processQR', { data: event.detail });">
            <h2 class="text-xl font-bold mb-4">Scan Absensi Guru</h2>

            <!-- Kamera -->
            <div id="reader" style="width:300px; height:300px; margin: 0 auto; display:none;"></div>

            <!-- Tombol kontrol kamera -->
            <div class="mb-4">
                <button id="camera-button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-2 w-full">
                    Aktifkan Kamera
                </button>
                <button id="stop-button" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mb-2 w-full hidden">
                    Hentikan Kamera
                </button>
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
        let isScanning = false;

        document.addEventListener("DOMContentLoaded", function () {
            const cameraButton = document.getElementById('camera-button');
            const stopButton = document.getElementById('stop-button');
            const reader = document.getElementById('reader');

            cameraButton.addEventListener('click', function () {
                if (isScanning) return;

                html5QrCode = new Html5Qrcode("reader");
                reader.style.display = 'block';

                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    onScanFailure
                ).then(() => {
                    cameraButton.classList.add('hidden');
                    stopButton.classList.remove('hidden');
                    isScanning = true;
                }).catch(err => {
                    alert("Tidak dapat mengakses kamera: " + err);
                });
            });

            stopButton.addEventListener('click', function () {
                html5QrCode.stop().then(() => {
                    reader.style.display = 'none';
                    cameraButton.classList.remove('hidden');
                    stopButton.classList.add('hidden');
                    isScanning = false;
                }).catch(err => {
                    alert("Gagal menghentikan kamera: " + err);
                });
            });

            function onScanSuccess(decodedText, decodedResult) {
                window.dispatchEvent(new CustomEvent('qr-scanned', { detail: decodedText }));
                html5QrCode.stop();
            }

            function onScanFailure(error) {
                // Kosongkan agar tidak spam error setiap frame
            }

            document.getElementById('file-input').addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (!file) return;

                html5QrCode = new Html5Qrcode("reader");

                html5QrCode.scanFile(file, true)
                    .then(decodedText => {
                        window.dispatchEvent(new CustomEvent('qr-scanned', { detail: decodedText }));
                    })
                    .catch(err => {
                        const errorEl = document.getElementById('file-error');
                        errorEl.classList.remove('hidden');
                        errorEl.textContent = 'Gagal membaca QR code: ' + err;
                    });
            });

            // Restart scanning otomatis setelah absensi berhasil
            window.addEventListener('absensi-recorded', () => {
                setTimeout(() => {
                    if (!isScanning) {
                        cameraButton.click();
                    }
                }, 3000);
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
