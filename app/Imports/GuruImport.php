<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\MataPelajaran; // Penting untuk mencari ID Mata Pelajaran
use App\Models\Kelas; // Penting untuk mencari ID Kelas (wali kelas)
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Validation\Rule;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Konversi Tanggal Lahir
        $tanggalLahir = null;
        if (isset($row['tanggal_lahir']) && $row['tanggal_lahir'] !== null) {
            if (is_numeric($row['tanggal_lahir'])) {
                try {
                    $tanggalLahir = Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalLahir = null;
                }
            } else {
                try {
                    $tanggalLahir = Carbon::createFromFormat('d-m-Y', $row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalLahir = null;
                }
            }
        }

        // Cari ID mata pelajaran berdasarkan nama
        $mata_pelajaran_diampu = (string) ($row['mata_pelajaran_diampu'] ?? '');
        if (empty($mata_pelajaran_diampu) || $mata_pelajaran_diampu === '-') {
            $mata_pelajaran_diampu = null;
        }
        $mataPelajaran = MataPelajaran::where('nama', $row['mata_pelajaran_diampu'] ?? null)->first();

        // Cari ID kelas berdasarkan nama (untuk wali kelas)
        $wali_kelas = (string) ($row['wali_kelas'] ?? '');
        if (empty($wali_kelas) || $wali_kelas === '-') {
            $wali_kelas = null;
        }
        $kelas = Kelas::where('nama', $row['wali_kelas'] ?? null)->first();

        // Pastikan niy disimpan sebagai string, bahkan jika hanya angka
        $niy = (string) ($row['niy'] ?? '');
        if (empty($niy) || $niy === '-') {
            $niy = null;
        }

        // Pastikan jenis_kelamin dan status disimpan lowercase untuk enum
        $jenisKelamin = strtolower($row['jenis_kelamin'] ?? '');
        $statusGuru = strtolower($row['status'] ?? 'aktif'); // Default 'aktif' jika kosong

        return new Guru([
            'niy'                   => $niy,
            'nama_lengkap'          => $row['nama_lengkap'] ?? '',
            'jenis_kelamin'         => $jenisKelamin,
            'tempat_lahir'          => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'         => $tanggalLahir,
            'agama'                 => $row['agama'] ?? null,
            'status'                => $statusGuru,
            'mata_pelajaran_id'     => $mataPelajaran->id ?? null,
            'kelas_id'              => $kelas->id ?? null,
            // 'user_id' dan 'qr_code_data' diabaikan jika tidak ada di Excel
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'niy'                   => ['nullable', 'max:255', Rule::unique('guru', 'niy')],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'jenis_kelamin'         => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'tempat_lahir'          => ['nullable', 'string', 'max:255'],
            'tanggal_lahir'         => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (is_numeric($value)) {
                        try {
                            Date::excelToDateTimeObject($value);
                        } catch (\Exception $e) {
                            $fail('Kolom Tanggal Lahir harus dalam format tanggal Excel yang valid.');
                        }
                    } elseif (is_string($value)) {
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
            'mata_pelajaran_diampu' => ['nullable', 'string'], // Validasi nama mata pelajaran
            'wali_kelas'            => ['nullable', 'string'], // Validasi nama kelas
            'status'                => ['nullable', Rule::in(['aktif', 'non-aktif'])],
        ];
    }

    /**
     * Custom validation messages.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'niy.max'                   => 'niy maksimal :max karakter.',
            'niy.unique'                => 'niy ini sudah terdaftar di database.',

            'nama_lengkap.required'     => 'Nama Lengkap wajib diisi.',
            'nama_lengkap.string'       => 'Nama Lengkap harus berupa teks.',
            'nama_lengkap.max'          => 'Nama Lengkap maksimal :max karakter.',

            'jenis_kelamin.required'    => 'Jenis Kelamin wajib diisi.',
            'jenis_kelamin.in'          => 'Jenis Kelamin harus "laki-laki" atau "perempuan".',

            'tempat_lahir.string'       => 'Tempat Lahir harus berupa teks.',
            'tempat_lahir.max'          => 'Tempat Lahir maksimal :max karakter.',

            'agama.in'                  => 'Agama tidak valid. Pilih dari: Islam, Kristen Protestan, Kristen Katolik, Hindu, Buddha, Konghucu.',

            'mata_pelajaran_diampu.exists' => 'Nama Mata Pelajaran yang diampu tidak ditemukan dalam database.',
            'wali_kelas.exists'         => 'Nama Wali Kelas tidak ditemukan dalam database.',

            'status.in'                 => 'Status Guru tidak valid. Pilih dari: aktif, non-aktif.',
        ];
    }
}