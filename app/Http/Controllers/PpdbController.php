<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PpdbController extends Controller
{
    public function index() {
        return view('pages.ppdb');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gelombang_id' => 'required|exists:gelombang,id',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
        ]);

        $path = null;
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('images/pp');
        }

        $nisnEmpat = substr(preg_replace('/\D/', '', $request->nisn), 0, 4); // hanya ambil angka, 4 digit awal
        $tahun = now()->format('Y');
        $nomor_pendaftaran = "G{$request->gelombang_id} {$nisnEmpat} {$tahun}";


        $calonSiswa = CalonSiswa::create([
            'gelombang_id' => $request->gelombang_id,
            'nomor_pendaftaran' => $nomor_pendaftaran,
            'nisn' => $request->nisn,
            'nama_lengkap' => $request->nama_lengkap,
            'profile_picture' => $path,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'nomor_hp_siswa' => $request->nomor_hp_siswa,
            'asal_sekolah' => $request->asal_sekolah,
            'anak_ke' => $request->anak_ke,
            'jumlah_saudara' => $request->jumlah_saudara,
            'cita_cita' => $request->cita_cita,
            'hobi' => $request->hobi,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'riwayat_penyakit' => $request->riwayat_penyakit,
            'nama_ayah' => $request->nama_ayah,
            'status_ayah' => $request->status_ayah,
            'tempat_lahir_ayah' => $request->tempat_lahir_ayah,
            'tanggal_lahir_ayah' => $request->tanggal_lahir_ayah,
            'pendidikan_ayah' => $request->pendidikan_ayah,
            'pekerjaan_ayah' => $request->pekerjaan_ayah,
            'penghasilan_ayah' => $request->penghasilan_ayah,
            'nomor_hp_ayah' => $request->nomor_hp_ayah,
            'nama_ibu' => $request->nama_ibu,
            'status_ibu' => $request->status_ibu,
            'tempat_lahir_ibu' => $request->tempat_lahir_ibu,
            'tanggal_lahir_ibu' => $request->tanggal_lahir_ibu,
            'pendidikan_ibu' => $request->pendidikan_ibu,
            'pekerjaan_ibu' => $request->pekerjaan_ibu,
            'penghasilan_ibu' => $request->penghasilan_ibu,
            'nomor_hp_ibu' => $request->nomor_hp_ibu,
            'tanggal_pendaftaran' => now(),
        ]);

        return view('ppdb.success', compact('calonSiswa'))->with('success', 'Pendaftaran Anda berhasil dikirim!');
        // return redirect()->route('ppdb.success')->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function indexSuccess() {
        return view('pages.ppdb_success');
    }
}
