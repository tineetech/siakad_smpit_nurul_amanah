<?php

namespace App\Imports;

use App\Models\Staf;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Validation\Rule;

class StafImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Fungsi helper untuk mengubah '-' atau string kosong menjadi null
        $toNullIfEmptyOrDash = function ($value) {
            return (empty($value) || $value === '-') ? null : $value;
        };

        // Konversi Tanggal Lahir
        $tanggalLahir = null;
        $tanggalLahirRaw = $row['tanggal_lahir'] ?? null;
        if ($tanggalLahirRaw !== null) {
            if (is_numeric($tanggalLahirRaw)) {
                try {
                    $tanggalLahir = Date::excelToDateTimeObject($tanggalLahirRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalLahir = null; // Biarkan validasi menangani ini jika perlu
                }
            } else {
                try {
                    $tanggalLahir = Carbon::createFromFormat('d-m-Y', $tanggalLahirRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalLahir = null; // Biarkan validasi menangani ini jika perlu
                }
            }
        }

        // Pastikan niy disimpan sebagai string, bahkan jika hanya angka
        $niy = (string) ($row['niy'] ?? '');
        if (empty($niy) || $niy === '-') {
            $niy = null;
        }

        // Pastikan jenis_kelamin, jabatan, dan status disimpan lowercase untuk enum
        $jenisKelamin = strtolower($row['jenis_kelamin'] ?? '');
        $jabatan = strtolower($row['jabatan'] ?? '');
        $statusStaf = strtolower($row['status'] ?? 'aktif'); // Default 'aktif' jika kosong

        return new Staf([
            'niy'                   => $niy,
            'nama_lengkap'          => $toNullIfEmptyOrDash($row['nama_lengkap'] ?? ''),
            'jabatan'               => $toNullIfEmptyOrDash($jabatan),
            'jenis_kelamin'         => $toNullIfEmptyOrDash($jenisKelamin),
            'tempat_lahir'          => $toNullIfEmptyOrDash($row['tempat_lahir'] ?? null),
            'tanggal_lahir'         => $tanggalLahir, // Sudah ditangani di atas
            'agama'                 => $toNullIfEmptyOrDash($row['agama'] ?? null),
            'status'                => $toNullIfEmptyOrDash($statusStaf),
            // 'user_id' diabaikan karena akan dibuat terpisah atau saat pendaftaran user
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'niy'                   => ['nullable', 'max:255', Rule::unique('staf', 'niy')],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'jabatan'               => ['required', Rule::in(['tata usaha', 'kepala sekolah', 'administrasi', 'panitia ppdb'])],
            'jenis_kelamin'         => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'tempat_lahir'          => ['nullable'],
            // Custom rule untuk Tanggal Lahir (memperbolehkan numeric Excel atau string DD-MM-YYYY)
            'tanggal_lahir'         => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '' || $value === '-') {
                        return; // Jika nullable, kosong, atau '-', valid
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
            'agama'                 => ['nullable'],
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

            'jabatan.required'          => 'Jabatan wajib diisi.',
            'jabatan.in'                => 'Jabatan tidak valid. Pilih dari: tata usaha, kepala sekolah, administrasi, panitia ppdb.',

            'jenis_kelamin.required'    => 'Jenis Kelamin wajib diisi.',
            'jenis_kelamin.in'          => 'Jenis Kelamin harus "laki-laki" atau "perempuan".',

            'tempat_lahir.string'       => 'Tempat Lahir harus berupa teks.',
            'tempat_lahir.max'          => 'Tempat Lahir maksimal :max karakter.',

            'agama.in'                  => 'Agama tidak valid. Pilih dari: Islam, Kristen Protestan, Kristen Katolik, Hindu, Buddha, Konghucu.',

            'status.in'                 => 'Status Staf tidak valid. Pilih dari: aktif, non-aktif.',
        ];
    }
}