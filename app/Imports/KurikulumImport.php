<?php

namespace App\Imports;

use App\Models\Kurikulum;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\Rule;

class KurikulumImport implements ToModel, WithHeadingRow, WithValidation
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

        // Konversi 'Aktif' (Ya/Tidak) ke boolean
        $isAktif = false; // Default ke false
        $aktifValue = strtolower($row['aktif'] ?? '');
        if (in_array($aktifValue, ['ya', 'true', '1', 'yes', 'on'])) {
            $isAktif = true;
        }

        return new Kurikulum([
            'nama'              => $row['nama_kurikulum'], // Wajib ada
            'deskripsi'         => $toNullIfEmptyOrDash($row['deskripsi'] ?? null),
            'tahun_mulai'       => $toNullIfEmptyOrDash($row['tahun_mulai'] ?? null),
            'tahun_berakhir'    => $toNullIfEmptyOrDash($row['tahun_berakhir'] ?? null),
            'is_aktif'          => $isAktif ?? false,
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_kurikulum'    => ['required', 'string', 'max:255'],
            'deskripsi'         => ['nullable'],
            'tahun_mulai'       => ['nullable', 'digits:4'],
            'tahun_berakhir'    => ['nullable', 'digits:4'],
            'aktif'             => ['nullable', 'string', Rule::in(['Ya', 'Tidak', 'ya', 'tidak', 'true', 'false', '1', '0'])],
        ];
    }

    /**
     * Custom validation messages.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_kurikulum.required' => 'Nama Kurikulum wajib diisi.',
            'nama_kurikulum.unique'   => 'Nama Kurikulum ini sudah terdaftar.',
            'tahun_mulai.digits'      => 'Tahun Mulai harus 4 digit angka.',
            'tahun_berakhir.digits'   => 'Tahun Berakhir harus 4 digit angka.',
            'aktif.in'                => 'Kolom Aktif harus diisi "Ya" atau "Tidak".',
        ];
    }
}