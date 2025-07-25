<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Filament\Resources\KelasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification; // Pastikan ini diimport

class CreateKelas extends CreateRecord
{
    protected static string $resource = KelasResource::class;

    protected function afterCreate(): void
    {
        // Panggil metode syncEnrollments dari KelasResource
        KelasResource::syncEnrollments($this->data, $this->record, 'create');
    }
}