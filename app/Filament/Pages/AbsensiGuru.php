<?php

namespace App\Filament\Pages;

use App\Models\Guru;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;

class AbsensiGuru extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static string $view = 'filament.pages.absensi-guru';
    protected static ?string $title = 'QR Absensi';
    protected static ?string $navigationLabel = 'QR Absensi Guru';
    protected static ?int $navigationSort = 3;

    // Use primitive types for Livewire compatibility
    public string $namaLengkap = '';
    public string $nip = '';
    public string $qrSvg = '';

    public static function canAccess(): bool
{
    return Auth::user()?->role === User::ROLE_GURU;
}

    public function mount(): void
    {
        $guru = Guru::where('user_id', Auth::id())->firstOrFail();
        
        $this->namaLengkap = $guru->nama_lengkap;
        $this->nip = $guru->nip;
        
        $qrData = json_encode([
            'guru_id' => $guru->id,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $this->qrSvg = QrCode::size(200)->generate($qrData);
    }

    public function getViewData(): array
    {
        return [
            'nama_lengkap' => $this->namaLengkap,
            'nip' => $this->nip,
            'qrSvg' => $this->qrSvg
        ];
    }
}