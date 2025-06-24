<?php

namespace App\Filament\Resources\PenetapanSppsResource\Pages;

use App\Filament\Resources\PenetapanSppsResource;
use App\Models\PenetapanSpps;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenetapanSpps extends EditRecord
{
    protected static string $resource = PenetapanSppsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $sppId = $data['id'];

        $lastestPenetapanSpp = PenetapanSpps::where('id', $sppId)
                                            ->whereNotNull('siswa_id')
                                            ->orderByDesc('created_at')
                                            ->first();

        if ($lastestPenetapanSpp) {
            $data['tanggal_jatuh_tempo'] = $lastestPenetapanSpp->tanggal_jatuh_tempo;
            $data['siswa_id_edit'] = $lastestPenetapanSpp->siswa_id;
            $data['pengaturan_spp_id_edit'] = $lastestPenetapanSpp->pengaturan_spp_id;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        PenetapanSppsResource::syncPenetapanSpp($this->data);
    }
    
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return new \App\Models\PenetapanSpps();
    }
}
