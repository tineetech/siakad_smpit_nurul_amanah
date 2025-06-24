<?php

namespace App\Filament\Navigation;

use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Auth;

class TeacherNavigation extends BaseNavigation
{
    public function items(): array
    {
        $items = [
            $this->makeItem('Data Siswa', route('filament.admin.resources.siswa.index'), 'heroicon-o-user-group', 'Data Master', 'viewAny siswas'),
            $this->makeItem('Data Calon Siswa', '#', 'heroicon-o-user-plus', 'Data Master', 'viewAny prospective-students'),
            $this->makeItem('Data Guru', route('filament.admin.resources.guru.index'), 'heroicon-o-academic-cap', 'Data Master', 'viewAny gurus'),
        ];

        /** @var \App\Models\User $user */
        if (Auth::user()->isAdmin()) {
            $items = array_merge($items, [
                $this->makeItem('Pantau Absensi Siswa', '#', 'heroicon-o-clipboard-document-check', 'Kelas', 'view class-attendance'),
                $this->makeItem('Jadwal Pembelajaran', '#', 'heroicon-o-calendar', 'Kelas', 'view class-schedule'),
                $this->makeItem('Data Siswa', '#', 'heroicon-o-user-group', 'Kelas', 'view class-students'),
            ]);
        }

        $items = array_merge($items, [
            $this->makeItem('Absensi', '#', 'heroicon-o-qr-code', 'Absensi'),
            $this->makeItem('History Absensi', '#', 'heroicon-o-clipboard-document-list', 'Absensi'),
        ]);

        return $items;
    }
}