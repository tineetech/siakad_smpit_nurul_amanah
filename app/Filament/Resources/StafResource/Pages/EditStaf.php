<?php

namespace App\Filament\Resources\StafResource\Pages;

use App\Filament\Resources\StafResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaf extends EditRecord
{
    protected static string $resource = StafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        // Update user for the guru
        StafResource::createUserForStaf($this->record);
    }
}
