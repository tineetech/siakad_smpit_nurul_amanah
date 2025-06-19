<?php

namespace App\Filament\Resources\PengumumanResource\Pages;

use App\Filament\Resources\PengumumanResource;
use App\Models\Pengumuman;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengumuman extends CreateRecord
{
    protected static string $resource = PengumumanResource::class;

    // --- Tambahkan method ini untuk mengirim notifikasi ---
    protected function afterCreate(): void // Perubahan di sini: visibility ke protected dan tidak perlu parameter langsung
    {
        // Ambil record yang baru saja dibuat
        $record = $this->record;

        // Tentukan siapa saja yang akan menerima notifikasi
        $targetUsers = collect();
        $targetPeran = $record->target_peran;

        if ($targetPeran === 'semua') {
            $targetUsers = User::all();
        } else if ($targetPeran) {
            $targetUsers = User::where('role', $targetPeran)->get();
        } else {
            // Default jika target_peran null atau tidak ditemukan
            // Anda bisa mengubah ini sesuai kebutuhan, misalnya hanya ke admin
            $targetUsers = User::all();
        }

        // Buat notifikasi
        $notification = Notification::make()
            ->title('Pengumuman Baru: ' . $record->judul)
            ->body(str()->limit($record->konten, 100) . '...') // Ambil 100 karakter pertama
            ->icon('heroicon-o-megaphone')
            ->iconColor('info')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->url(PengumumanResource::getUrl('edit', ['record' => $record])), // PERBAIKAN DI SINI
            ])
            ->sendToDatabase($targetUsers); // Kirim notifikasi ke user yang dituju dan simpan di DB
    }
}