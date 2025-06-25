<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class ScanAbsensiSiswa extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static string $view = 'filament.pages.scan-absensi-siswa';
    protected static ?string $title = 'Scan Absensi Siswa';

    #[On('processQR')]
    public function processQR($data)
    {
        try {
            $decoded = json_decode($data, true);


            if (!isset($decoded['siswa_id'], $decoded['timestamp'])) {
                Notification::make()
                    ->title('QR Tidak Valid')
                    ->body('Format QR tidak sesuai.')
                    ->danger()
                    ->send();
                return;
            }

            $siswa = Siswa::find($decoded['siswa_id']);

            if (!$siswa) {
                Notification::make()
                    ->title('Siswa Tidak Ditemukan')
                    ->body('Data siswa tidak ditemukan.')
                    ->danger()
                    ->send();
                return;
            }

            $today = now()->toDateString();
            $existingAbsensi = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->where('tanggal_absensi', $today)
                ->first();

            if ($existingAbsensi) {
                Notification::make()
                    ->title('Sudah Absen')
                    ->body("Siswa {$siswa->nama} sudah absen hari ini.")
                    ->warning()
                    ->send();
                return;
            }

            AbsensiSiswa::create([
                'siswa_id' => $siswa->id,
                'tanggal_absensi' => $today,
                'waktu_absensi' => now()->toTimeString(),
                'status_kehadiran' => 'Hadir',
                'mode_absensi' => 'scan_qr',
                'pencatat_user_id' => Auth::id(),
                'qr_code_terscan' => $data['data'],
                'catatan' => null,
            ]);

            Notification::make()
                ->title('Absensi Berhasil')
                ->body("Absensi {$siswa->nama} berhasil dicatat.")
                ->success()
                ->send();

            $this->js('window.dispatchEvent(new CustomEvent("absensi-recorded"));');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'tata_usaha', 'guru']);
    }
}
