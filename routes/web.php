<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\PengumumanHomeController;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\SiswaController;
use App\Models\Gelombang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

Route::get('/', function () {
    return view('pages.beranda');
})->name('home');

Route::prefix('spmb')->group(function() {
    Route::get('/', [PpdbController::class, 'index'])->name('spmb.index');
    Route::post('/', [PpdbController::class, 'store'])->name('spmb.store');

    Route::get('/success/{nomor_pendaftaran}', [PpdbController::class, 'indexSuccess'])->name('spmb.success');
});


Route::prefix('api')->group(function() {
    Route::get('/gelombang', function () {
        return Gelombang::select('id', 'nama')->orderBy('tanggal_mulai', 'desc')->get();
    })->name('api.gelombang');

    // pengumuman route
    Route::get('/announcements', [PengumumanHomeController::class, 'index'])->name('api.announcements.index');
    Route::get('/announcements/{id}', [PengumumanHomeController::class, 'show'])->name('api.announcements.show');
});



Route::get('/jadwal', function () {
    // Dummy Data untuk Kelas, Semester, Mata Pelajaran, Guru
    $kelasDummy = (object)[
        'id' => 1,
        'nama' => 'Kelas X MIPA 1'
    ];

    $semesterDummy = (object)[
        'id' => 1,
        'nama' => 'Ganjil 2024/2025'
    ];

    $mataPelajaranDummy = [
        1 => (object)['id' => 1, 'nama' => 'Matematika'],
        2 => (object)['id' => 2, 'nama' => 'Fisika'],
        3 => (object)['id' => 3, 'nama' => 'Kimia'],
        4 => (object)['id' => 4, 'nama' => 'Biologi'],
        5 => (object)['id' => 5, 'nama' => 'Bahasa Indonesia'],
        6 => (object)['id' => 6, 'nama' => 'Sejarah'],
    ];

    $guruDummy = [
        1 => (object)['id' => 1, 'nama' => 'Budi Santoso'],
        2 => (object)['id' => 2, 'nama' => 'Siti Aminah'],
        3 => (object)['id' => 3, 'nama' => 'Joko Susilo'],
    ];

    // Dummy Jadwal Pelajaran (meniru struktur relasi eager loading)
    $jadwalDummy = [
        [
            'hari' => 'Senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
            'kelas_id' => 1,
            'mata_pelajaran_id' => 1,
            'guru_id' => 1,
            'semester_id' => 1,
            'kurikulum_id' => 1,
            'mataPelajaran' => $mataPelajaranDummy[1],
            'guru' => $guruDummy[1],
            'semester' => $semesterDummy,
        ],
        [
            'hari' => 'Senin',
            'jam_mulai' => '09:15',
            'jam_selesai' => '10:45',
            'kelas_id' => 1,
            'mata_pelajaran_id' => 2,
            'guru_id' => 2,
            'semester_id' => 1,
            'kurikulum_id' => 1,
            'mataPelajaran' => $mataPelajaranDummy[2],
            'guru' => $guruDummy[2],
            'semester' => $semesterDummy,
        ],
        // Hanya ada satu jadwal di hari Selasa
        [
            'hari' => 'Selasa',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'kelas_id' => 1,
            'mata_pelajaran_id' => 3,
            'guru_id' => 3,
            'semester_id' => 1,
            'kurikulum_id' => 1,
            'mataPelajaran' => $mataPelajaranDummy[3],
            'guru' => $guruDummy[3],
            'semester' => $semesterDummy,
        ],
        // Hari Rabu tidak ada jadwal sama sekali
        [
            'hari' => 'Kamis',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:30',
            'kelas_id' => 1,
            'mata_pelajaran_id' => 4,
            'guru_id' => 1,
            'semester_id' => 1,
            'kurikulum_id' => 1,
            'mataPelajaran' => $mataPelajaranDummy[4],
            'guru' => $guruDummy[1],
            'semester' => $semesterDummy,
        ],
        [
            'hari' => 'Jumat',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'kelas_id' => 1,
            'mata_pelajaran_id' => 5,
            'guru_id' => 2,
            'semester_id' => 1,
            'kurikulum_id' => 1,
            'mataPelajaran' => $mataPelajaranDummy[5],
            'guru' => $guruDummy[2],
            'semester' => $semesterDummy,
        ],
        // Hari Sabtu tidak ada jadwal
    ];

    // Konversi dummy jadwal menjadi Collection agar bisa menggunakan groupBy
    $jadwalCollection = new Collection($jadwalDummy);

    $data = [
        'kelas' => $kelasDummy,
        'jadwal' => $jadwalCollection,
        'semester' => $semesterDummy,
    ];

    $pdf = Pdf::loadView('jadwal-pdf', $data);

    // Untuk melihat langsung di browser (bukan download)
    // return $pdf->stream('jadwal-kelas-dummy.pdf');

    // Untuk mengunduh file
    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->output();
    }, 'jadwal-kelas-dummy.pdf');
    // return view('jadwal-pdf', $data);
})->name('export-jadwal-dummy');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

