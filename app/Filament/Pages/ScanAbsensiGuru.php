<?php

namespace App\Filament\Pages;

use App\Models\AbsensiGuru;
use App\Models\Guru;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScanAbsensiGuru extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static string $view = 'filament.pages.scan-absensi-guru';
    protected static ?string $title = 'Scan Absensi Guru';
    protected static ?string $navigationLabel = 'Scan Absensi Guru';
    protected static ?int $navigationSort = 5;

    public string $status = '';
    public string $message = '';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'tata_usaha']);
    }

    protected function getListeners()
    {
        return [
            'processQR' => 'processQR',
        ];
    }

    public function processQR($data)
    {
        try {
            $decoded = json_decode($data, true);
            
            if (!isset($decoded['type']) || $decoded['type'] !== 'guru') {
                throw new \Exception('QR code tidak valid untuk absensi guru');
            }
            
            // Validasi hash
            $expectedHash = md5($decoded['guru_id'] . config('app.key'));
            if ($decoded['hash'] !== $expectedHash) {
                throw new \Exception('QR code tidak valid');
            }
            
            $guru = Guru::findOrFail($decoded['guru_id']);
            
            // Cek apakah sudah absen hari ini
            $existingAbsensi = AbsensiGuru::where('guru_id', $guru->id)
                ->whereDate('tanggal_absensi', now()->toDateString())
                ->first();
                
            if ($existingAbsensi) {
                $this->status = 'warning';
                $this->message = 'Guru ' . $guru->nama_lengkap . ' sudah melakukan absensi hari ini pada ' . 
                               $existingAbsensi->waktu_absensi . ' (' . 
                               ucfirst($existingAbsensi->status_kehadiran) . ')';
                return;
            }
            
            // Tentukan status kehadiran
            $waktuAbsen = now();
            $jamBatas = now()->setHour(8)->setMinute(0)->setSecond(0); // Batas jam 08:00
            
            $statusKehadiran = $waktuAbsen->gt($jamBatas) ? 
                AbsensiGuru::STATUS_TERLAMBAT : 
                AbsensiGuru::STATUS_HADIR;
            
            // Simpan absensi
            AbsensiGuru::create([
                'guru_id' => $guru->id,
                'tanggal_absensi' => $waktuAbsen->toDateString(),
                'waktu_absensi' => $waktuAbsen->toTimeString(),
                'status_kehadiran' => $statusKehadiran,
                'mode_absensi' => AbsensiGuru::MODE_QR,
                'pencatat_user_id' => Auth::id(),
                'qr_code_terscan' => $data,
                'catatan' => 'Absensi via QR Code'
            ]);
            
            $this->status = 'success';
            $this->message = 'Absensi berhasil: ' . $guru->nama_lengkap . 
                           ' (' . ucfirst($statusKehadiran) . ') ' . 
                           $waktuAbsen->format('H:i:s');
            
            // Reset status after 5 seconds
            $this->dispatchBrowserEvent('absensi-recorded');
            
        } catch (\Exception $e) {
            Log::error('QR Absensi Error: ' . $e->getMessage(), [
                'qr_data' => $data,
                'user' => Auth::id()
            ]);
            
            $this->status = 'danger';
            $this->message = 'Gagal melakukan absensi: ' . $e->getMessage();
        }
    }
}