<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;

class RaportExportController extends Controller
{
    public function export(Request $request)
    {
        $siswa = Siswa::with('kelas')->findOrFail($request->siswa_id);
        $semester = Semester::findOrFail($request->semester_id);
        
        $nilaiList = Nilai::with('mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->where('semester_id', $semester->id)
            ->orderBy('mata_pelajaran_id')
            ->get();

        $pdf = Pdf::loadView('exports.raport', [
            'siswa' => $siswa,
            'semester' => $semester,
            'nilaiList' => $nilaiList,
        ]);

        return $pdf->stream('raport_'.$siswa->nama_lengkap.'.pdf');
    }
}
