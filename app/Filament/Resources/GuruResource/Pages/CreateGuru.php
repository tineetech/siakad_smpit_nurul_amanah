<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGuru extends CreateRecord
{
    protected static string $resource = GuruResource::class;

    protected function afterCreate(): void
    {
        // Create user for the new guru
        GuruResource::createUserForGuru($this->record);
        
        // Generate and store QR code
        // $this->generateAndStoreQrCode($this->record);
    }
    
    protected function generateAndStoreQrCode($guru): void
    {
        $qrData = json_encode([
            'user_id' => $guru->user_id,
            'niy' => $guru->niy,
            'nama' => $guru->nama_lengkap,
            'kelas_id' => $guru->kelas_id,
        ]);

        // Store only the data, not the image
        $guru->qr_code_data = $qrData;
        $guru->save();
    }
}