<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected function afterSave(): void
    {
        // Update user for the siswa
        SiswaResource::createUserForSiswa($this->record);
        
        // Regenerate QR code if needed
        if (!$this->record->qr_code_data) {
            $this->generateAndStoreQrCode($this->record);
        }
    }

    protected function generateAndStoreQrCode($siswa): void
    {
        $qrData = json_encode([
            'user_id' => $siswa->user_id,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama_lengkap,
            'kelas_id' => $siswa->kelas_id,
        ]);

        $siswa->qr_code_data = $qrData;
        $siswa->save();
    }
}