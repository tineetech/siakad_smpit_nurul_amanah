<x-filament-panels::page>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
            {{ static::$title }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $nama_lengkap }} ({{ $nisn }})
        </p>


        <div class="flex justify-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-4 bg-white rounded">
                {!! $qrSvg !!}
            </div>
        </div>

        <div class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
            Scan QR code ini untuk absensi
        </div>
    </div>

</x-filament-panels::page>