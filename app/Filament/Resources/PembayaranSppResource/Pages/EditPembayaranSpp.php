<?php

namespace App\Filament\Resources\PembayaranSppResource\Pages;

use App\Filament\Resources\PembayaranSppResource;
use App\Models\PembayaranSpp;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranSpp extends EditRecord
{
    protected static string $resource = PembayaranSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }    
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $sppId = $data['id'];

        $lastestPenetapanSpp = PembayaranSpp::where('id', $sppId)
                                            ->whereNotNull('siswa_id')
                                            ->whereNotNull('penetapan_spp_id')
                                            ->orderByDesc('created_at')
                                            ->first();

        if ($lastestPenetapanSpp) {
            $data['status'] = $lastestPenetapanSpp->penetapan->status;
        }

        return $data;
    }

    
    protected function afterSave(): void
    {
        if ($this->record->penetapan) {
            $this->record->penetapan->update([
                'status' => $this->data['status'],
            ]);
        }
    }
}
