<?php

namespace App\Imports;

use App\Models\Pengumuman;
use App\Models\User; // Import model User
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Validation\Rule;

class PengumumanImport implements ToModel, WithHeadingRow, WithValidation
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

        // Konversi Tanggal Publikasi
        $tanggalPublikasi = null;
        $tanggalPublikasiRaw = $row['tanggal_publikasi'] ?? null;
        if ($tanggalPublikasiRaw !== null) {
            if (is_numeric($tanggalPublikasiRaw)) {
                try {
                    // Excel timestamp is days since 1900-01-01 (or 1904-01-01 for Mac)
                    $tanggalPublikasi = Date::excelToDateTimeObject($tanggalPublikasiRaw);
                } catch (\Exception $e) {
                    $tanggalPublikasi = null;
                }
            } else {
                try {
                    $tanggalPublikasi = Carbon::parse($tanggalPublikasiRaw); // Parse string date/datetime
                } catch (\Exception $e) {
                    $tanggalPublikasi = null;
                }
            }
        }

        // Cari ID user berdasarkan nama (misal: "admin", "tata_usaha") atau nama lengkap user
        $dipostingOlehUser = null;
        if (isset($row['diposting_oleh']) && $row['diposting_oleh'] !== null) {
            $dipostingOlehUser = User::where('name', $row['diposting_oleh'])
                                     ->orWhere('email', $row['diposting_oleh'])
                                     ->first();
        }

        return new Pengumuman([
            'judul'                  => $row['judul'], // Wajib ada
            'konten'                 => $row['konten'], // Wajib ada
            'diposting_oleh_user_id' => $dipostingOlehUser->id ?? Auth::user()->id, // Default ke user yang import jika tidak ditemukan
            'target_peran'           => $toNullIfEmptyOrDash(strtolower($row['target_peran'] ?? null)), // Pastikan lowercase
            'tanggal_publikasi'      => $tanggalPublikasi,
        ]);
    }

    /**
     * Define validation rules for imported data.
     * @return array
     */
    public function rules(): array
    {
        return [
            'judul'             => ['required', 'string', 'max:255'],
            'konten'            => ['required', 'string'],
            'diposting_oleh'    => ['nullable'], // Validasi nama/email user
            'target_peran'      => ['nullable', 'string', Rule::in(['semua', 'siswa', 'guru', 'tata_usaha', 'staff_ppdb', 'admin', 'staff'])],
            'tanggal_publikasi' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '' || $value === '-') { return; }
                    if (is_numeric($value)) {
                        try { Date::excelToDateTimeObject($value); } catch (\Exception $e) { $fail('Kolom Tanggal Publikasi harus dalam format tanggal Excel yang valid.'); }
                    } elseif (is_string($value)) {
                        try { Carbon::parse($value); } catch (\Exception $e) { $fail('Kolom Tanggal Publikasi harus dalam format tanggal/waktu yang valid (misal: YYYY-MM-DD HH:MM:SS atau DD-MM-YYYY HH:MM:SS).'); }
                    } else { $fail('Kolom Tanggal Publikasi harus berupa angka atau string tanggal/waktu yang valid.'); }
                },
            ],
        ];
    }

    /**
     * Custom validation messages.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'judul.required'              => 'Judul Pengumuman wajib diisi.',
            'konten.required'             => 'Konten Pengumuman wajib diisi.',
            'diposting_oleh.exists'       => 'User yang memposting tidak ditemukan dalam database.',
            'target_peran.in'             => 'Target Peran tidak valid. Pilih dari: semua, siswa, guru, tata_usaha, staff_ppdb, admin, staff.',
            'tanggal_publikasi.valid_date' => 'Format Tanggal Publikasi tidak valid.',
        ];
    }
}