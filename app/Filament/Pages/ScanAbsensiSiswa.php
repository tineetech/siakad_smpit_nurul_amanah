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
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 1;
    public bool $notifikasiTerkirim = false;

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'tata_usaha', 'guru']);
    }

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
                $this->js('window.dispatchEvent(new CustomEvent("absensi-error"));');
                return;
            }

            $siswa = Siswa::find($decoded['siswa_id']);

            if (!$siswa) {
                if (!$this->notifikasiTerkirim) {
                    Notification::make()
                        ->title('Siswa Tidak Ditemukan')
                        ->body('Data siswa tidak ditemukan.')
                        ->danger()
                        ->send();

                    $this->notifikasiTerkirim = true;
                    $this->js('window.dispatchEvent(new CustomEvent("absensi-error"));');
                }
                return;
            }

            $today = now()->toDateString();
            $existingAbsensi = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->where('tanggal_absensi', $today)
                ->first();

            if ($existingAbsensi) {
                if (!$this->notifikasiTerkirim) {
                    Notification::make()
                        ->title('Sudah Absen')
                        ->body("Siswa {$siswa->nama} sudah absen hari ini.")
                        ->warning()
                        ->send();

                    $this->notifikasiTerkirim = true;
                    $this->js('window.dispatchEvent(new CustomEvent("absensi-error"));');
                }
                return;
            }


            AbsensiSiswa::create([
                'siswa_id' => $siswa->id,
                'tanggal_absensi' => $today,
                'waktu_absensi' => now()->toTimeString(),
                'status_kehadiran' => 'hadir',
                'mode_absensi' => 'scan_qr',
                'pencatat_user_id' => Auth::id(),
                'qr_code_terscan' => $data,
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
            $this->js('window.dispatchEvent(new CustomEvent("absensi-error"));');
        }
    }

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'guru']);
    }
}
