<?php

namespace App\Filament\Pages;

use App\Models\Guru;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;

class QrCodeGuru extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static string $view = 'filament.pages.qr-code-guru';
    protected static ?string $title = 'QR Code Absensi Guru';
    protected static ?string $navigationLabel = 'QR Code Absensi';
    protected static ?int $navigationSort = 4;

    public string $namaGuru = '';
    public string $nip = '';
    public string $qrSvg = '';

    public static function canAccess(): bool
    {
        return Auth::user()?->role === 'guru';
    }

    public function mount(): void
    {
        $guru = Guru::where('user_id', Auth::id())->firstOrFail();
        
        $this->namaGuru = $guru->nama_lengkap;
        $this->nip = $guru->nip ?? '';
        
        $qrData = json_encode([
            'guru_id' => $guru->id,
            'type' => 'guru',
            'nama' => $guru->nama_lengkap,
            'timestamp' => now()->toDateTimeString(),
            'hash' => md5($guru->id . config('app.key'))
        ]);
        
        $this->qrSvg = QrCode::size(300)->generate($qrData);
    }
}