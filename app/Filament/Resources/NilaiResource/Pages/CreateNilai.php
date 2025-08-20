<?php

namespace App\Filament\Resources\NilaiResource\Pages;

use App\Filament\Resources\NilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNilai extends CreateRecord
{
    protected static string $resource = NilaiResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Ambil data siswa untuk tahu kelas_id-nya
        $siswa = \App\Models\Siswa::findOrFail($data['siswa_id']);

        foreach ($data['mapel_nilai'] as $item) {
            \App\Models\Nilai::create([
                'semester_id' => $data['semester_id'],
                'siswa_id' => $data['siswa_id'],
                'kelas_id' => $siswa->kelas_id, // tambahkan kelas_id
                'mata_pelajaran_id' => $item['mata_pelajaran_id'],
                'nilai_harian' => $item['nilai_harian'] ?? null,
                'nilai_pas' => $item['nilai_pas'] ?? null,
                'nilai_akhir' => $item['nilai_akhir'] ?? null,
                'nilai_kkm' => $item['nilai_kkm'] ?? null,
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }

        // Return dummy model supaya Filament tidak error redirect
        return new \App\Models\Nilai();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
