<?php

namespace App\Filament\Resources\PengaturanPembayaranResource\Pages;

use App\Filament\Resources\PengaturanPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanPembayaran extends EditRecord
{
    protected static string $resource = PengaturanPembayaranResource::class;
    protected static ?string $title = "Edit Pengaturan Pembayaran";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
