<?php

namespace App\Filament\Navigation;

use Filament\Navigation\NavigationItem;

class StudentNavigation extends BaseNavigation
{
    public function items(): array
    {
        return [
            $this->makeItem('Jadwal Pembelajaran', '#', 'heroicon-o-calendar', 'Kelas', 'view class-schedule'),
            $this->makeItem('Data Siswa', '#', 'heroicon-o-user-group', 'Kelas', 'view class-students'),
            $this->makeItem('Absensi', '#', 'heroicon-o-qr-code', 'Absensi'),
            $this->makeItem('History Absensi', '#', 'heroicon-o-clipboard-document-list', 'Absensi'),
        ];
    }
}