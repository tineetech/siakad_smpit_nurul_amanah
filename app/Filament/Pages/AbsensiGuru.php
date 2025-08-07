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
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 0;

    // Use primitive types for Livewire compatibility
    public string $namaLengkap = '';
    public string $niy = '';
    public string $qrSvg = '';

    public static function canAccess(): bool
    {
        return Auth::user()->role === User::ROLE_GURU;
        // return in_array(Auth::user()?->role, ['admin', 'kepala_sekolah']);
    }

    public function mount(): void
    {
        $guru = Guru::where('user_id', Auth::id())->firstOrFail();

        $this->namaLengkap = $guru->nama_lengkap;
        $this->niy = $guru->niy;

        // Tambahkan hash dan type agar validasi backend cocok
        $hash = md5($guru->id . config('app.key'));

        $qrData = json_encode([
            'type' => 'guru',
            'guru_id' => $guru->id,
            'hash' => $hash,
            'timestamp' => now()->toDateTimeString()  // opsional saja, boleh ada boleh tidak
        ]);

        $this->qrSvg = QrCode::size(200)->generate($qrData);
    }


    public function getViewData(): array
    {
        return [
            'nama_lengkap' => 'Guru '. $this->namaLengkap,
            'niy' => $this->niy,
            'qrSvg' => $this->qrSvg
        ];
    }
}
