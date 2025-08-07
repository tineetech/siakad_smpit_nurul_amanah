<?php

namespace App\Filament\Resources\LaporanNilaiSiswaResource\Pages;

use App\Filament\Resources\LaporanNilaiSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanNilaiSiswa extends EditRecord
{
    protected static string $resource = LaporanNilaiSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
