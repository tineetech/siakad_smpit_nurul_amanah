<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\Rule; // Tambahkan ini
use Illuminate\Support\Facades\Storage; // Tambahkan ini

class PpdbController extends Controller
{
    public function index() {
        return view('pages.spmb');
    }

    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'gelombang_id' => 'required|exists:gelombang,id',
        //     'nik' => 'required|unique:calon_siswa,nik',
        //     'nama_lengkap' => 'required|string|max:255',
        //     'jenis_kelamin' => 'required|in:laki-laki,perempuan',
        //     // Validasi untuk file
        //     'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2MB
        //     'kartu_keluarga' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'], // Max 5MB
        //     'akta_kelahiran' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'], // Max 5MB
        //     'surat_kelulusan' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'], // Max 5MB
        // ]);

        $validated = $request->validate([
            'gelombang_id' => 'required|exists:gelombang,id',
            'nik' => 'required|unique:calon_siswa,nik',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'profile_picture' => ['required','image','mimes:jpeg,png,jpg,gif','max:2048'],
            'kartu_keluarga' => ['required','file','mimes:jpeg,png,jpg,gif,pdf','max:5120'],
            'akta_kelahiran' => ['required','file','mimes:jpeg,png,jpg,gif,pdf','max:5120'],
            'surat_kelulusan' => ['required','file','mimes:jpeg,png,jpg,gif,pdf','max:5120'],
        ], [
            // Pesan custom
            'gelombang_id.required' => 'Gelombang harus dipilih.',
            'gelombang_id.exists' => 'Gelombang tidak ditemukan.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan.',
            'profile_picture.required' => 'Foto profil wajib diupload.',
            'profile_picture.image' => 'Foto profil harus berupa gambar.',
            'profile_picture.mimes' => 'Foto profil harus berformat jpeg, png, jpg, atau gif.',
            'profile_picture.max' => 'Ukuran foto profil maksimal 2MB.',
            'kartu_keluarga.required' => 'Kartu Keluarga wajib diupload.',
            'akta_kelahiran.required' => 'Akta kelahiran wajib diupload.',
            'surat_kelulusan.required' => 'Surat kelulusan wajib diupload.',
        ]);


        $nisnEmpat = substr(preg_replace('/\D/', '', $request->nisn), 0, 4); // hanya ambil angka, 4 digit awal
        $tahun = now()->format('Y');
        $nomor_pendaftaran = "G{$request->gelombang_id}-{$nisnEmpat}-{$tahun}"; // Using hyphen for readability

        // Simpan file ke storage
        $paths = [];
        foreach (['profile_picture', 'kartu_keluarga', 'akta_kelahiran', 'surat_kelulusan'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                // Nama folder di dalam storage/app/public
                $folder = 'spmb/' . $field;
                // Nama file unik (misalnya, timestamp_originalfilename.ext)
                $fileName = time() . '_' . $file->getClientOriginalName();
                // Simpan file ke disk 'public'
                $paths[$field] = $file->storeAs($folder, $fileName, 'public');
            }
        }
        $gelombang = Gelombang::where('id', $request->gelombang_id)->whereColumn('kouta_terisi', '<', 'kouta')->first();
        if ($gelombang) {
            $gelombang->increment('kouta_terisi');
        } else {
            return back()->withErrors(['gelombang_id' => 'Gelombang tidak valid atau kuota sudah penuh.']);
        }

        $calonSiswa = CalonSiswa::create([
            'gelombang_id' => $request->gelombang_id,
            'nomor_pendaftaran' => $nomor_pendaftaran,
            'nisn' => $request->nisn,
            'nik' => $request->nik,
            'nama_lengkap' => $request->nama_lengkap,
            'profile_picture' => $paths['profile_picture'] ?? null,
            'kartu_keluarga' => $paths['kartu_keluarga'] ?? null,
            'akta_kelahiran' => $paths['akta_kelahiran'] ?? null,
            'surat_kelulusan' => $paths['surat_kelulusan'] ?? null,
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

         return redirect()->route('spmb.success', ['nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran])
                         ->with('success', 'Pendaftaran Anda berhasil dikirim!');
    }

    /**
     * Display the PPDB success page with calon siswa details.
     *
     * @param string $nomor_pendaftaran
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function indexSuccess($nomor_pendaftaran)
    {
        $calonSiswa = CalonSiswa::where('nomor_pendaftaran', $nomor_pendaftaran)->first();

        if (!$calonSiswa) {
            // Handle case where registration number is not found
            return redirect()->route('spmb.index')->with('error', 'Nomor pendaftaran tidak ditemukan.');
        }

        return view('pages.spmb_success', compact('calonSiswa'));
    }
}
