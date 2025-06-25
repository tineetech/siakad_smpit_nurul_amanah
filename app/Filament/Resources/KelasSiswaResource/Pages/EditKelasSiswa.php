<?php

namespace App\Filament\Resources\KelasSiswaResource\Pages;

use App\Filament\Resources\KelasSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKelasSiswa extends EditRecord
{
    protected static string $resource = KelasSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
