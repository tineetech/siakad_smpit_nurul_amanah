<?php
namespace App\Filament\Resources\PenetapanSppsResource\Pages;

use App\Filament\Resources\PenetapanSppsResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePenetapanSpps extends CreateRecord
{
    protected static string $resource = PenetapanSppsResource::class;

    protected function afterCreate(): void
    {
        PenetapanSppsResource::syncPenetapanSpp($this->data);
    }
    
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return new \App\Models\PenetapanSpps();
    }

    protected function getRedirectUrl(): string
    {
        return PenetapanSppsResource::getUrl('index');
    }

}
