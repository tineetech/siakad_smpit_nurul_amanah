<?php

namespace App\Filament\Navigation;

use Filament\Navigation\NavigationItem;

class StaffNavigation extends BaseNavigation
{
    public function items(): array
    {
        return [
            $this->makeItem('Pembuatan SPP', '#', 'heroicon-o-document-plus', 'SPP', 'create spp'),
            $this->makeItem('Penetapan SPP', '#', 'heroicon-o-document-check', 'SPP', 'assign spp'),
            $this->makeItem('Pembayaran SPP', '#', 'heroicon-o-banknotes', 'SPP', 'process spp-payment'),
            $this->makeItem('Data Calon Siswa', '#', 'heroicon-o-user-plus', 'PPDB', 'viewAny prospective-students'),
            $this->makeItem('Data Siswa Aktif', route('filament.admin.resources.siswa.index'), 'heroicon-o-user-group', 'PPDB', 'viewAny siswas'),
            $this->makeItem('History Transaksi', '#', 'heroicon-o-document-chart-bar', 'Transaksi'),
        ];
    }
}