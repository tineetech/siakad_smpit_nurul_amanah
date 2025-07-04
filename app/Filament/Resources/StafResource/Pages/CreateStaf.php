<?php

namespace App\Filament\Resources\StafResource\Pages;

use App\Filament\Resources\StafResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStaf extends CreateRecord
{
    protected static string $resource = StafResource::class;
    
    protected function afterCreate(): void
    {
        // Create user for the new guru
        StafResource::createUserForStaf($this->record);
    }
}
