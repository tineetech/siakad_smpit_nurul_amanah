<?php

namespace App\Filament\Resources\LaporanNilaiSiswaResource\Pages;

use App\Filament\Resources\LaporanNilaiSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanNilaiSiswas extends ListRecords
{
    protected static string $resource = LaporanNilaiSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
