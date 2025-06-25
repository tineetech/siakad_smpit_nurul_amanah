<?php

namespace App\Filament\Resources\PengumumanResource\Pages;

use App\Filament\Resources\PengumumanResource;
use App\Models\Pengumuman;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification; // Pastikan import ini, BUKAN Filament\Notifications\Notification
use App\Notifications\NewAnnouncementNotification; // Kita akan membuat kelas notifikasi ini

use Carbon\Carbon;

class CreatePengumuman extends CreateRecord
{
    protected static string $resource = PengumumanResource::class;

    protected function afterCreate(): void
    {
        $this->sendDatabaseNotification($this->record);
    }

    /**
     * Mengirim notifikasi ke database pengguna berdasarkan target peran pengumuman.
     *
     * @param \App\Models\Pengumuman $pengumuman
     * @return void
     */
    protected function sendDatabaseNotification(Pengumuman $pengumuman): void
    {
        // Jika tanggal publikasi pengumuman di masa depan, jangan kirim notifikasi sekarang.
        if ($pengumuman->tanggal_publikasi && $pengumuman->tanggal_publikasi->isFuture()) {
            // Beri tahu user yang membuat pengumuman bahwa notifikasi akan dikirim nanti
            \Filament\Notifications\Notification::make() // Gunakan Notifikasi Filament untuk pemberitahuan ke pembuat
                ->title('Pengumuman Berhasil Dibuat (Tertunda)')
                ->body('Pengumuman "' . $pengumuman->judul . '" akan dipublikasikan pada ' . $pengumuman->tanggal_publikasi->format('d M Y H:i') . '. Notifikasi akan dikirim sesuai jadwal.')
                ->warning()
                ->send();
            return; // Hentikan proses pengiriman notifikasi database jika tertunda
        }

        $targetRole = $pengumuman->target_peran;
        $usersToNotify = new Collection();

        if ($targetRole === 'semua') {
            // Ambil semua user kecuali user yang membuat pengumuman itu sendiri (opsional)
            $usersToNotify = User::get();
        } else {
            switch ($targetRole) {
                case 'siswa':
                    $usersToNotify = User::where('role', User::ROLE_SISWA)->get();
                    break;
                case 'guru':
                    $usersToNotify = User::where('role', User::ROLE_GURU)->get();
                    break;
                case 'tata_usaha':
                    $usersToNotify = User::where('role', User::ROLE_TATA_USAHA)->get();
                    break;
                case 'staff_ppdb':
                    $usersToNotify = User::where('role', User::ROLE_STAFF_PPDB)->get();
                    break;
                case 'admin':
                    $usersToNotify = User::where('role', User::ROLE_ADMIN)->get();
                    break;
                case 'staff':
                    $usersToNotify = User::whereIn('role', [
                        User::ROLE_STAFF, // Asumsi ada ROLE_STAFF
                        // Tambahkan peran lain yang termasuk 'staff lainnya' jika ada
                    ])->get();
                    break;
                default:
                    break;
            }
        }

        if ($usersToNotify->isNotEmpty()) {
            // Kirim notifikasi ke koleksi pengguna menggunakan facade Notification
            Notification::send($usersToNotify, new NewAnnouncementNotification($pengumuman));

            // Notifikasi popup ke pembuat pengumuman (opsional, untuk konfirmasi)
            \Filament\Notifications\Notification::make()
                ->title('Pengumuman dan Notifikasi Berhasil Dibuat!')
                ->body('Notifikasi pengumuman "' . $pengumuman->judul . '" telah dikirim ke ' . $usersToNotify->count() . ' pengguna yang ditargetkan.')
                ->success()
                ->send();
        } else {
             \Filament\Notifications\Notification::make()
                ->title('Pengumuman Berhasil Dibuat, Tanpa Notifikasi Database')
                ->body('Tidak ada pengguna yang cocok dengan target peran "' . ($targetRole ?? 'Tidak Ditentukan') . '" untuk dikirimi notifikasi.')
                ->warning()
                ->send();
        }
    }
}