<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\Rule;

class MataPelajaranImport implements ToModel, WithHeadingRow, WithValidation
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

        return new MataPelajaran([
            'nama'  => $row['nama_mata_pelajaran'], // Wajib ada
            'kode'  => $toNullIfEmptyOrDash($row['kode'] ?? null),
            'jenis' => $toNullIfEmptyOrDash(strtolower($row['jenis'] ?? null)), // Pastikan lowercase untuk enum
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_mata_pelajaran' => ['required', 'string', 'max:255', Rule::unique('mata_pelajaran', 'nama')],
            'kode'                => ['nullable', 'string', 'max:50', Rule::unique('mata_pelajaran', 'kode')],
            'jenis'               => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_mata_pelajaran.required' => 'Nama Mata Pelajaran wajib diisi.',
            'nama_mata_pelajaran.unique'   => 'Nama Mata Pelajaran ini sudah terdaftar.',
            'kode.unique'                  => 'Kode Mata Pelajaran ini sudah terdaftar.',
            'jenis.in'                     => 'Jenis Mata Pelajaran tidak valid. Pilih dari: reguler, kepesantrenan, ekstrakurikuler.',
        ];
    }
}