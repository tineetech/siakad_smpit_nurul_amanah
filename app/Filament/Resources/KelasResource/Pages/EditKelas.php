<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Filament\Resources\KelasResource;
use App\Models\Enrollment;
use App\Models\Semester; // Pastikan ini diimport
use App\Models\Kurikulum; // Pastikan ini diimport
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditKelas extends EditRecord
{
    protected static string $resource = KelasResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $kelasId = $data['id'];

        // Cari enrollment siswa terakhir (atau yang pertama) untuk kelas ini.
        // Ini akan digunakan untuk menentukan semester dan kurikulum yang akan ditampilkan secara default.
        $latestStudentEnrollment = Enrollment::where('kelas_id', $kelasId)
                                            ->whereNotNull('siswa_id') // Hanya fokus pada enrollment siswa
                                            ->orderByDesc('created_at') // Ambil yang terbaru jika ada banyak
                                            ->first();

        if ($latestStudentEnrollment) {
            // Jika ada enrollment siswa, gunakan semester dan kurikulum dari enrollment tersebut
            $data['selected_semester_id'] = $latestStudentEnrollment->semester_id;
            $data['selected_kurikulum_id'] = $latestStudentEnrollment->kurikulum_id;

            // Sekarang, ambil semua ID siswa yang terdaftar di kelas ini
            // untuk kombinasi semester dan kurikulum yang telah ditentukan di atas
            $enrolledSiswaIds = Enrollment::where('kelas_id', $kelasId)
                                          ->where('semester_id', $data['selected_semester_id'])
                                          ->where('kurikulum_id', $data['selected_kurikulum_id'])
                                          ->whereNotNull('siswa_id')
                                          ->pluck('siswa_id')
                                          ->toArray();

            $data['enrolled_siswa'] = $enrolledSiswaIds;
        } else {
            // Jika tidak ada enrollment siswa untuk kelas ini,
            // set default ke semester/kurikulum aktif dan array siswa kosong
            $data['selected_semester_id'] = Semester::where('is_aktif', true)->first()?->id;
            $data['selected_kurikulum_id'] = Kurikulum::where('is_aktif', true)->first()?->id;
            $data['enrolled_siswa'] = [];
        }

        return $data;
    }

    protected function afterSave(): void
    {
        KelasResource::syncEnrollments($this->data, $this->record);
    }
}