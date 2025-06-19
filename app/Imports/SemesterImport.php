<?php

namespace App\Imports;

use App\Models\Semester;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Validation\Rule;

class SemesterImport implements ToModel, WithHeadingRow, WithValidation
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

        // Konversi Tanggal Mulai
        $tanggalMulai = null;
        $tanggalMulaiRaw = $row['tanggal_mulai'] ?? null;
        if ($tanggalMulaiRaw !== null) {
            if (is_numeric($tanggalMulaiRaw)) {
                try {
                    $tanggalMulai = Date::excelToDateTimeObject($tanggalMulaiRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalMulai = null;
                }
            } else {
                try {
                    $tanggalMulai = Carbon::createFromFormat('d-m-Y', $tanggalMulaiRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalMulai = null;
                }
            }
        }

        // Konversi Tanggal Berakhir
        $tanggalBerakhir = null;
        $tanggalBerakhirRaw = $row['tanggal_berakhir'] ?? null;
        if ($tanggalBerakhirRaw !== null) {
            if (is_numeric($tanggalBerakhirRaw)) {
                try {
                    $tanggalBerakhir = Date::excelToDateTimeObject($tanggalBerakhirRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalBerakhir = null;
                }
            } else {
                try {
                    $tanggalBerakhir = Carbon::createFromFormat('d-m-Y', $tanggalBerakhirRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalBerakhir = null;
                }
            }
        }

        // Konversi 'Aktif' (Ya/Tidak) ke boolean
        $isAktif = false; // Default ke false
        $aktifValue = strtolower($row['aktif'] ?? '');
        if (in_array($aktifValue, ['ya', 'true', '1', 'yes', 'on'])) {
            $isAktif = true;
        }

        return new Semester([
            'nama'              => $row['nama_semester'], // Wajib ada, tidak perlu toNullIfEmptyOrDash
            'tanggal_mulai'     => $tanggalMulai,
            'tanggal_berakhir'  => $tanggalBerakhir,
            'is_aktif'          => $isAktif ?? false, // Default false jika tidak ada/null
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_semester'    => ['required', 'string', 'max:255'],
            'tanggal_mulai'    => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '' || $value === '-') { return; }
                    if (is_numeric($value)) {
                        try { Date::excelToDateTimeObject($value); } catch (\Exception $e) { $fail('Kolom Tanggal Mulai harus dalam format tanggal Excel yang valid.'); }
                    } elseif (is_string($value)) {
                        try { Carbon::createFromFormat('d-m-Y', $value); } catch (\Exception $e) { $fail('Kolom Tanggal Mulai harus dalam format DD-MM-YYYY atau format tanggal Excel yang valid.'); }
                    } else { $fail('Kolom Tanggal Mulai harus berupa angka atau string tanggal yang valid.'); }
                },
            ],
            'tanggal_berakhir' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '' || $value === '-') { return; }
                    if (is_numeric($value)) {
                        try { Date::excelToDateTimeObject($value); } catch (\Exception $e) { $fail('Kolom Tanggal Berakhir harus dalam format tanggal Excel yang valid.'); }
                    } elseif (is_string($value)) {
                        try { Carbon::createFromFormat('d-m-Y', $value); } catch (\Exception $e) { $fail('Kolom Tanggal Berakhir harus dalam format DD-MM-YYYY atau format tanggal Excel yang valid.'); }
                    } else { $fail('Kolom Tanggal Berakhir harus berupa angka atau string tanggal yang valid.'); }
                },
            ],
            'aktif'            => ['nullable', 'string'], // Agar fleksibel
        ];
    }

    /**
     * Custom validation messages.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_semester.required' => 'Nama Semester wajib diisi.',
            'nama_semester.unique'   => 'Nama Semester ini sudah terdaftar.',
            'aktif.in'               => 'Kolom Aktif harus diisi "Ya" atau "Tidak".',
        ];
    }
}