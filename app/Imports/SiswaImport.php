<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon; // Untuk parsing tanggal
use PhpOffice\PhpSpreadsheet\Shared\Date; // Untuk konversi tanggal Excel
use Illuminate\Validation\Rule; // Untuk Rule::unique dan Rule::in

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Debugging: Anda bisa uncomment baris di bawah untuk melihat data mentah per baris
        // dd($row);

        // --- Penanganan kolom yang mungkin null atau perlu konversi ---

        // Konversi Tanggal Lahir
        $tanggalLahir = null;
        if (isset($row['tanggal_lahir']) && $row['tanggal_lahir'] !== null) {
            if (is_numeric($row['tanggal_lahir'])) {
                // Jika dari Excel numeric date (seringkali hasil export)
                try {
                    $tanggalLahir = Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Biarkan validasi menangani jika konversi numeric gagal
                    $tanggalLahir = null;
                }
            } else {
                // Jika sudah berupa string (misal, diketik manual di Excel), coba parse 'DD-MM-YYYY'
                try {
                    $tanggalLahir = Carbon::createFromFormat('d-m-Y', $row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Jika format string tidak sesuai 'DD-MM-YYYY', biarkan validasi menangani
                    $tanggalLahir = null;
                }
            }
        }

        // Cari ID kelas berdasarkan nama kelas dari Excel
        // Maatwebsite/Excel akan mengonversi 'Kelas' (dari header) menjadi 'kelas' (key array)
        $kelas = Kelas::where('nama', $row['kelas'] ?? null)->first(); // Gunakan null coalescing operator

        // Pastikan NISN dan NIS disimpan sebagai string, bahkan jika hanya angka
        $nisn = (string) ($row['nisn'] ?? '');
        $nis = isset($row['nis']) && $row['nis'] !== null && $row['nis'] !== '-' ? (string) $row['nis'] : null;

        // Pastikan jenis_kelamin dan status disimpan lowercase untuk enum
        $jenisKelamin = strtolower($row['jenis_kelamin'] ?? '');
        $statusSiswa = strtolower($row['status_siswa'] ?? 'aktif'); // Default 'aktif' jika kosong

        // Handle nomor telepon, pastikan string
        $nomorTeleponOrtu = (string) ($row['no_hp_orang_tua'] ?? '');
        if (empty($nomorTeleponOrtu) || $nomorTeleponOrtu === '-') {
            $nomorTeleponOrtu = null;
        }


        // Buat instance model Siswa
        return new Siswa([
            'nisn'                  => $nisn,
            'nis'                   => $nis,
            'nama_lengkap'          => $row['nama_lengkap'] ?? '',
            'jenis_kelamin'         => $jenisKelamin,
            'tempat_lahir'          => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'         => $tanggalLahir,
            'agama'                 => $row['agama'] ?? null,
            'nama_ayah'             => $row['nama_ayah'] ?? null,
            'nama_ibu'              => $row['nama_ibu'] ?? null,
            'nomor_telepon_orang_tua' => $nomorTeleponOrtu,
            'status'                => $statusSiswa,
            'kelas_id'              => $kelas->id ?? null,
            // 'user_id' => null, // Jika ini diatur di tempat lain atau opsional
            // 'qr_code_data' => null, // Jika ini diatur di tempat lain atau opsional
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'nisn'                  => ['required', 'max:255', Rule::unique('siswa', 'nisn')],
            'nis'                   => ['nullable', 'max:255', Rule::unique('siswa', 'nis')],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'jenis_kelamin'         => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'tempat_lahir'          => ['nullable', 'string', 'max:255'],
            // Custom rule untuk Tanggal Lahir (sesuai PendudukImport)
            'tanggal_lahir'         => [
                'nullable', // Mengubah 'required' menjadi 'nullable'
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') {
                        return; // Jika nullable dan kosong, tidak perlu validasi lebih lanjut
                    }

                    if (is_numeric($value)) { // Kemungkinan dari Excel numeric date
                        try {
                            Date::excelToDateTimeObject($value);
                        } catch (\Exception $e) {
                            $fail('Kolom Tanggal Lahir harus dalam format tanggal Excel yang valid.');
                        }
                    } elseif (is_string($value)) { // Kemungkinan string 'DD-MM-YYYY'
                        try {
                            Carbon::createFromFormat('d-m-Y', $value);
                        } catch (\Exception $e) {
                            $fail('Kolom Tanggal Lahir harus dalam format DD-MM-YYYY atau format tanggal Excel yang valid.');
                        }
                    } else {
                        $fail('Kolom Tanggal Lahir harus berupa angka atau string tanggal yang valid.');
                    }
                },
            ],
            'agama'                 => ['nullable', Rule::in(['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu'])],
            'nama_ayah'             => ['nullable', 'string', 'max:255'],
            'nama_ibu'              => ['nullable', 'string', 'max:255'],
            'no_hp_orang_tua'       => ['nullable', 'string', 'max:20'],
            // Pastikan 'kelas' ada di tabel 'kelas' pada kolom 'nama'
            'kelas'                 => ['nullable', 'string', Rule::exists('kelas', 'nama')],
            'status_siswa'          => ['nullable', Rule::in(['aktif', 'non-aktif', 'lulus'])],
        ];
    }

    /**
     * Custom validation messages.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nisn.required'         => 'NISN wajib diisi.',
            'nisn.string'           => 'NISN harus berupa teks.',
            'nisn.max'              => 'NISN maksimal :max karakter.',
            'nisn.unique'           => 'NISN ini sudah terdaftar di database.',

            'nis.string'            => 'NIS harus berupa teks.',
            'nis.max'               => 'NIS maksimal :max karakter.',
            'nis.unique'            => 'NIS ini sudah terdaftar di database.',

            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'nama_lengkap.string'   => 'Nama Lengkap harus berupa teks.',
            'nama_lengkap.max'      => 'Nama Lengkap maksimal :max karakter.',

            'jenis_kelamin.required' => 'Jenis Kelamin wajib diisi.',
            'jenis_kelamin.in'      => 'Jenis Kelamin harus "laki-laki" atau "perempuan".',

            'tempat_lahir.string'   => 'Tempat Lahir harus berupa teks.',
            'tempat_lahir.max'      => 'Tempat Lahir maksimal :max karakter.',

            // Pesan untuk custom rule tanggal_lahir akan ditangani langsung di rule
            'tanggal_lahir.nullable' => 'Tanggal Lahir harus diisi atau dikosongkan.', // Ini akan muncul jika tidak ada value sama sekali

            'agama.in'              => 'Agama tidak valid. Pilih dari: Islam, Kristen Protestan, Kristen Katolik, Hindu, Buddha, Konghucu.',

            'nama_ayah.string'      => 'Nama Ayah harus berupa teks.',
            'nama_ibu.string'       => 'Nama Ibu harus berupa teks.',
            'no_hp_orang_tua.string' => 'Nomor HP Orang Tua harus berupa teks.',
            'no_hp_orang_tua.max'   => 'Nomor HP Orang Tua maksimal :max karakter.',

            'kelas.exists'          => 'Nama Kelas tidak ditemukan dalam database. Pastikan nama kelas di Excel sudah benar.',
            'kelas.string'          => 'Kolom Kelas harus berupa teks.',

            'status_siswa.in'       => 'Status Siswa tidak valid. Pilih dari: aktif, non-aktif, lulus.',
        ];
    }
}