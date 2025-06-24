<?php

// In SiswaResource.php, update the create and edit pages:

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function afterCreate(): void
    {
        // Create user for the new siswa
        SiswaResource::createUserForSiswa($this->record);
        
        // Generate and store QR code
        $this->generateAndStoreQrCode($this->record);
    }

    protected function generateAndStoreQrCode($siswa): void
    {
        $qrData = json_encode([
            'user_id' => $siswa->user_id,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama_lengkap,
            'kelas_id' => $siswa->kelas_id,
        ]);

        // Store only the data, not the image
        $siswa->qr_code_data = $qrData;
        $siswa->save();
    }
}