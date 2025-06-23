<?php

namespace App\Filament\Resources\PengaturanSppResource\Pages;

use App\Filament\Resources\PengaturanSppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanSpp extends EditRecord
{
    protected static string $resource = PengaturanSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
