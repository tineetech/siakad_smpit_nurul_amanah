<?php

namespace App\Filament\Pages;

use App\Models\AbsensiGuru;
use App\Models\Guru;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class ScanAbsensiGuru extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static string $view = 'filament.pages.scan-absensi-guru';
    protected static ?string $title = 'Scan Absensi Guru';
    protected static ?string $navigationLabel = 'Scan Absensi Guru';
    protected static ?int $navigationSort = 5;

    #[On('processQR')]
    public function processQR($data)
    {
        try {
            $decoded = json_decode($data, true);

            // Validasi QR Code
            if (!isset($decoded['type'], $decoded['guru_id'], $decoded['hash']) || $decoded['type'] !== 'guru') {
                Notification::make()
                    ->title('QR Tidak Valid')
                    ->body('QR code tidak valid untuk absensi guru.')
                    ->danger()
                    ->send();
                return;
            }

            // Validasi hash keamanan
            $expectedHash = md5($decoded['guru_id'] . config('app.key'));
            if ($decoded['hash'] !== $expectedHash) {
                Notification::make()
                    ->title('QR Tidak Valid')
                    ->body('QR code tidak valid atau telah dimanipulasi.')
                    ->danger()
                    ->send();
                return;
            }

            $guru = Guru::find($decoded['guru_id']);

            if (!$guru) {
                Notification::make()
                    ->title('Guru Tidak Ditemukan')
                    ->body('Data guru tidak ditemukan.')
                    ->danger()
                    ->send();
                return;
            }

            // Cek apakah sudah absen hari ini
            $today = now()->toDateString();
            $existingAbsensi = AbsensiGuru::where('guru_id', $guru->id)
                ->where('tanggal_absensi', $today)
                ->first();

            if ($existingAbsensi) {
                Notification::make()
                    ->title('Sudah Absen')
                    ->body("Guru {$guru->nama_lengkap} sudah absen hari ini pada {$existingAbsensi->waktu_absensi} ({$existingAbsensi->status_kehadiran}).")
                    ->warning()
                    ->send();
                return;
            }

            // Penentuan status kehadiran
            $waktuAbsen = now();
            $jamBatas = now()->setHour(8)->setMinute(0)->setSecond(0);
            $statusKehadiran = $waktuAbsen->gt($jamBatas) ? AbsensiGuru::STATUS_TERLAMBAT : AbsensiGuru::STATUS_HADIR;

            AbsensiGuru::create([
                'guru_id' => $guru->id,
                'tanggal_absensi' => $today,
                'waktu_absensi' => $waktuAbsen->toTimeString(),
                'status_kehadiran' => $statusKehadiran,
                'mode_absensi' => AbsensiGuru::MODE_QR,
                'pencatat_user_id' => Auth::id(),
                'qr_code_terscan' => $data,
                'catatan' => 'Absensi via QR Code',
            ]);

            Notification::make()
                ->title('Absensi Berhasil')
                ->body("Absensi {$guru->nama_lengkap} berhasil dicatat sebagai '{$statusKehadiran}'.")
                ->success()
                ->send();

            $this->js('window.dispatchEvent(new CustomEvent("absensi-recorded"));');
        } catch (\Exception $e) {
            Log::error('QR Absensi Error: ' . $e->getMessage(), [
                'qr_data' => $data,
                'user' => Auth::id(),
            ]);

            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'tata_usaha']);
    }
}
