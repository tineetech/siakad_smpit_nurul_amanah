<?php

namespace App\Filament\Resources\PengaturanPembayaranResource\Pages;

use App\Filament\Resources\PengaturanPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengaturanPembayaran extends CreateRecord
{
    protected static string $resource = PengaturanPembayaranResource::class;
    protected static ?string $title = "Buat Pengaturan Pembayaran";
}
