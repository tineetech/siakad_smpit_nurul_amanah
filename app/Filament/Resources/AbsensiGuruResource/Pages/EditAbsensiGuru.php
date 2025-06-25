<?php

namespace App\Filament\Resources\AbsensiGuruResource\Pages;

use App\Filament\Resources\AbsensiGuruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiGuru extends EditRecord
{
    protected static string $resource = AbsensiGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
