<?php

namespace App\Filament\Resources\PengaturanPembayaranResource\Pages;

use App\Filament\Resources\PengaturanPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanPembayarans extends ListRecords
{
    protected static string $resource = PengaturanPembayaranResource::class;
    protected static ?string $title = "Pengaturan Pembayaran";
    protected static ?string $breadcrumb = 'Pengaturan Pembayaran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label("Buat Tagihan"),
        ];
    }

    
}
