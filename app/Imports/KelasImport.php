<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Guru; // Import model Guru untuk mencari ID wali kelas
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\Rule;

class KelasImport implements ToModel, WithHeadingRow, WithValidation
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

        $waliKelas = Guru::where('nama_lengkap', $toNullIfEmptyOrDash($row['wali_kelas'] ?? null))->first();

        return new Kelas([
            'nama'      => $row['nama_kelas'], // Wajib ada
            'tingkat'   => $toNullIfEmptyOrDash($row['tingkat'] ?? null),
            'kapasitas' => $toNullIfEmptyOrDash($row['kapasitas'] ?? null),
            'guru_id'   => $waliKelas->id ?? null, // Simpan ID guru jika ditemukan
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas', 'nama')],
            'tingkat'    => ['nullable', 'string', 'max:255'],
            'kapasitas'  => ['nullable', 'integer', 'min:1'],
            'wali_kelas' => ['nullable', 'string', Rule::exists('guru', 'nama_lengkap')], // Validasi nama lengkap guru
        ];
    }

    /**
     * Custom validation messages.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_kelas.required'     => 'Nama Kelas wajib diisi.',
            'nama_kelas.unique'       => 'Nama Kelas ini sudah terdaftar.',
            'kapasitas.integer'       => 'Kapasitas harus berupa angka.',
            'kapasitas.min'           => 'Kapasitas minimal :min.',
            'wali_kelas.exists'       => 'Nama Wali Kelas tidak ditemukan dalam database.',
        ];
    }
}