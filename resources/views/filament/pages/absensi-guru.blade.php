<x-filament-panels::page>
 <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold">QR Code Absensi Guru</h2>
            <p class="text-gray-600">Gunakan QR code berikut untuk absensi</p>
        </div>

        <div class="flex flex-col items-center justify-center p-6 bg-white rounded-lg shadow">
            <div class="mb-4 text-center">
                <p class="text-lg font-semibold">{{ $nama_lengkap }}</p>
                <p class="text-gray-600">NIP: {{ $nip }}</p>
            </div>
            
            <div class="p-4 bg-white border border-gray-200 rounded">
                {!! $qrSvg !!}
            </div>
            
            <div class="mt-4 text-sm text-gray-500 text-center">
                <p>QR Code ini bersifat pribadi dan unik</p>
                <p>Jangan bagikan kepada siapapun</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
