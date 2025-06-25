<?php
namespace App\Imports;

use App\Models\PenetapanSpps;
use App\Models\Siswa;
use App\Models\PengaturanSpp;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PenetapanSppImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $siswa = Siswa::where('nama_lengkap', $row['siswa'])->first();
        $pengaturan = PengaturanSpp::where('nama', $row['pengaturan_spp'])->first();

        if (!$siswa || !$pengaturan) {
            return null;
        }

        return new PenetapanSpps([
            'siswa_id' => $siswa->id,
            'pengaturan_spp_id' => $pengaturan->id,
            'status' => $row['status'] ?? 'belum_dibayar',
            'tanggal_jatuh_tempo' => Date::excelToDateTimeObject($row['jatuh_tempo'])->format('Y-m-d'),
        ]);
    }
}
