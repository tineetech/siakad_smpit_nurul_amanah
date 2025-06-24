<?php

namespace App\Filament\Navigation;

use Filament\Navigation\NavigationItem;
use app\Filament\Resources\SiswaResource;


class AdminNavigation extends BaseNavigation
{
    public function items(): array
    {
        return [
            $this->makeItem('Data Siswa', route('filament.admin.resources.siswa.list'), 'heroicon-o-user-group', 'Data Master', 'viewAny siswas'),
            $this->makeItem('Data Calon Siswa', '#', 'heroicon-o-user-plus', 'Data Master', 'viewAny prospective-students'),
            $this->makeItem('Data Guru', route('filament.admin.resources.guru.index'), 'heroicon-o-academic-cap', 'Data Master', 'viewAny gurus'),
            $this->makeItem('Data Staff/TU', route('filament.admin.resources.staf.index'), 'heroicon-o-briefcase', 'Data Master', 'viewAny staff'),
            $this->makeItem('Data Kelas', route('filament.admin.resources.kelas.index'), 'heroicon-o-building-office-2', 'Data Master', 'viewAny kelas'),
            $this->makeItem('Jadwal Pelajaran', '#', 'heroicon-o-calendar', 'Data Master', 'viewAny schedules'),
            $this->makeItem('Data Semester', route('filament.admin.resources.semester.index'), 'heroicon-o-calendar-days', 'Data Master', 'viewAny semesters'),
            $this->makeItem('Data Kurikulum', route('filament.admin.resources.kurikulum.index'), 'heroicon-o-book-open', 'Data Master', 'viewAny kurikulums'),
            $this->makeItem('Data Pengumuman', route('filament.admin.resources.pengumuman.index'), 'heroicon-o-megaphone', 'Data Master', 'viewAny announcements'),
            
            // Kesiswaan
            $this->makeItem('Absensi Siswa', '#', 'heroicon-o-clipboard-document-check', 'Kesiswaan', 'manage student-attendance'),
            $this->makeItem('Kelas Siswa', '#', 'heroicon-o-user-group', 'Kesiswaan', 'manage student-class'),
            $this->makeItem('Anggota Rombel Pembelajaran', '#', 'heroicon-o-users', 'Kesiswaan', 'manage study-group'),
            
            // Guru Management
            $this->makeItem('Absensi Guru', '#', 'heroicon-o-clipboard-document-list', 'Guru', 'manage teacher-attendance'),
            $this->makeItem('Penetapan Wali Kelas', '#', 'heroicon-o-identification', 'Guru', 'manage homeroom-teacher'),
            $this->makeItem('Penetapan Guru Pembelajaran', '#', 'heroicon-o-academic-cap', 'Guru', 'manage teaching-assignment'),
            
            // SPP Management
            $this->makeItem('Pengaturan SPP', route('filament.admin.resources.pengaturan-spp.index'), 'heroicon-o-cog-6-tooth', 'SPP', 'viewAny spp-settings'),
            $this->makeItem('Pembuatan SPP', '#', 'heroicon-o-document-plus', 'SPP', 'create spp'),
            $this->makeItem('Penetapan SPP', '#', 'heroicon-o-document-check', 'SPP', 'assign spp'),
            $this->makeItem('Pembayaran SPP Manual', '#', 'heroicon-o-banknotes', 'SPP', 'process spp-payment'),
            $this->makeItem('Rekap Transaksi Siswa', '#', 'heroicon-o-document-chart-bar', 'SPP', 'view spp-report'),
            
            // PPDB Management
            $this->makeItem('Approval Calon Siswa', '#', 'heroicon-o-document-check', 'PPDB', 'approve prospective-students'),
        ];
    }
}