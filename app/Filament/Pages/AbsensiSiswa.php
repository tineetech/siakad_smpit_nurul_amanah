<?php

namespace App\Filament\Pages;

use App\Models\Siswa;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;

class AbsensiSiswa extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static string $view = 'filament.pages.absensi-siswa';
    protected static ?string $title = 'QR Absensi Saya';
    protected static ?string $navigationLabel = 'QR Absensi';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 0;

    // Use primitive types for Livewire compatibility
    public string $namaLengkap = '';
    public string $nisn = '';
    public string $qrSvg = '';

    // Add this method to check if the page should be visible
    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['siswa', 'guru']);
    }

    public function mount(): void
    {
        $siswa = Siswa::where('user_id', Auth::id())->firstOrFail();
        
        $this->namaLengkap = $siswa->nama_lengkap;
        $this->nisn = $siswa->nisn;
        
        $qrData = json_encode([
            'siswa_id' => $siswa->id,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $this->qrSvg = QrCode::size(200)->generate($qrData);
    }

    public function getViewData(): array
    {
        return [
            'nama_lengkap' => $this->namaLengkap,
            'nisn' => $this->nisn,
            'qrSvg' => $this->qrSvg
        ];
    }
}