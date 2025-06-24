<x-filament-panels::page>
    <x-filament::card>
        <div class="text-center">
            <h2 class="text-xl font-bold mb-4">Scan Absensi Guru</h2>

            @if($status)
            <div class="p-4 mb-4 rounded-lg bg-{{ $status }}-100 text-{{ $status }}-800">
                {{ $message }}
            </div>
            @endif

            <div class="mb-4">
                <!-- Camera Scanner -->
                <div id="camera-section" style="width: 300px; margin: 0 auto;">
                    <div id="camera-error" class="hidden p-4 bg-red-100 text-red-800 rounded-lg mb-2">
                        Kamera tidak dapat diakses. Silakan coba alternatif berikut.
                    </div>
                    <video id="qr-video" style="width: 100%; display: none;"></video>
                    <button id="camera-button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-2 w-full">
                        Aktifkan Kamera
                    </button>
                </div>

                <!-- File Upload Fallback -->
                <div id="file-section" class="mt-4">
                    <p class="text-sm text-gray-600 mb-2">Atau upload gambar QR Code:</p>
                    <input type="file" id="file-input" accept="image/*" class="hidden">
                    <label for="file-input" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded inline-block cursor-pointer">
                        Pilih Gambar
                    </label>
                    <div id="file-error" class="hidden p-4 bg-red-100 text-red-800 rounded-lg mt-2"></div>
                </div>
            </div>

            <p class="text-sm text-gray-600">
                Arahkan kamera ke QR code guru atau upload gambar QR code untuk absensi
            </p>
        </div>
    </x-filament::card>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fallback sederhana hanya dengan file upload
        document.getElementById('file-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(event) {
                // Anggap file adalah gambar QR code yang sudah didecode
                // Untuk demo, kita langsung ambil nama file sebagai ID
                const fileName = file.name.replace(/\.[^/.]+$/, ""); // Hapus ekstensi
                window.Livewire.dispatch('qrScanned', {
                    guru_id: fileName,
                    timestamp: new Date().toISOString()
                });
            };
            reader.readAsDataURL(file);
        });
        
        // Sembunyikan fitur kamera jika tidak tersedia
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            document.getElementById('camera-section').classList.add('hidden');
        }
    });
</script>
@endpush
</x-filament-panels::page>