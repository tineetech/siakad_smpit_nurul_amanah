<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Staf;
use App\Models\Pengumuman;
use App\Models\CalonSiswa; // Import model CalonSiswa
use App\Models\Kelas; // Import model Kelas
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Admin dan Tata Usaha melihat statistik penuh
        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_TATA_USAHA])) {
            return [
                Stat::make('Total Siswa', Siswa::count())
                    ->description('Jumlah seluruh siswa aktif')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('info'),
                Stat::make('Total Guru', Guru::count())
                    ->description('Jumlah seluruh guru aktif')
                    ->descriptionIcon('heroicon-o-academic-cap')
                    ->color('success'),
                Stat::make('Total Staf', Staf::count())
                    ->description('Jumlah seluruh staf sekolah')
                    ->descriptionIcon('heroicon-o-building-office')
                    ->color('warning'),
                Stat::make('Pengumuman Aktif', Pengumuman::where('tanggal_publikasi', '<=', now())->count())
                    ->description('Jumlah pengumuman yang aktif')
                    ->descriptionIcon('heroicon-o-megaphone')
                    ->color('primary'),
                Stat::make('Calon Siswa Baru', CalonSiswa::whereDate('tanggal_pendaftaran', now()->toDateString())->count())
                    ->description('Jumlah pendaftar hari ini')
                    ->descriptionIcon('heroicon-o-user-plus')
                    ->color('secondary'),
                Stat::make('Kelas Tersedia', Kelas::count())
                    ->description('Jumlah kelas yang ada')
                    ->descriptionIcon('heroicon-o-building-library')
                    ->color('danger'),
            ];
        }

        // Contoh untuk Guru (sesuaikan dengan yang ingin dilihat guru)
        if ($user->role === User::ROLE_GURU) {
            $guru = Guru::where('user_id', $user->id)->first();
            // Pastikan relasi 'siswa' ada di model Guru jika Anda menggunakannya.
            // Jika Guru tidak langsung mengampu siswa dalam relasi `hasMany`,
            // mungkin Anda perlu mengubah logika ini (misalnya, menghitung siswa di kelas wali kelasnya).
            $namaKelas = $guru && $guru->kelas ? $guru->kelas->nama : 'Belum Ada';
              // Tentukan warna berdasarkan status siswa
            $statusColor = match ($guru->status ?? '') {
                'aktif' => 'success',
                'non-aktif' => 'warning',
                'lulus' => 'info',
                default => 'gray',
            };
            return [
                Stat::make('Status Pendaftaran', $guru->status ?? 'N/A')
                    ->description('Status pendaftaran Anda')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->color($statusColor), // Gunakan variabel yang sudah berisi string warna
                Stat::make('Kelas Anda', $namaKelas)
                    ->description('Kelas Anda saat ini')
                    ->descriptionIcon('heroicon-o-home')
                    ->color('info'),
                Stat::make('Pengumuman Terbaru', Pengumuman::where('target_peran', 'guru')
                                                            ->orWhere('target_peran', 'semua')
                                                            ->whereDate('tanggal_publikasi', '<=', now())
                                                            ->count())
                    ->description('Pengumuman baru untuk Anda')
                    ->descriptionIcon('heroicon-o-megaphone')
                    ->color('primary'),
            ];
        }

        // Contoh untuk Siswa (sesuaikan dengan yang ingin dilihat siswa)
        if ($user->role === User::ROLE_SISWA) {
            $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();
            $namaKelas = $siswa && $siswa->kelas ? $siswa->kelas->nama : 'Belum Ada';

            // Tentukan warna berdasarkan status siswa
            $statusColor = match ($siswa->status ?? '') {
                'aktif' => 'success',
                'non-aktif' => 'warning',
                'lulus' => 'info',
                default => 'gray',
            };

            return [
                Stat::make('Status Pendaftaran', $siswa->status ?? 'N/A')
                    ->description('Status pendaftaran Anda')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->color($statusColor), // Gunakan variabel yang sudah berisi string warna
                Stat::make('Kelas Anda', $namaKelas)
                    ->description('Kelas Anda saat ini')
                    ->descriptionIcon('heroicon-o-home')
                    ->color('info'),
                Stat::make('Pengumuman Terbaru', Pengumuman::where('target_peran', 'siswa')
                                                            ->orWhere('target_peran', 'semua')
                                                            ->whereDate('tanggal_publikasi', '<=', now())
                                                            ->count())
                    ->description('Pengumuman baru untuk Anda')
                    ->descriptionIcon('heroicon-o-megaphone')
                    ->color('primary'),
            ];
        }

        // Default jika role tidak dikenali
        return [];
    }
}