<?php

namespace App\Imports;

use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class PembayaranSppImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $siswa = Siswa::where('nama_lengkap', $row['nama_siswa'])->first();
        $teller = User::where('name', $row['teller'])->first();

        return new PembayaranSpp([
            'siswa_id' => $siswa?->id,
            'jumlah_dibayar' => $row['jumlah_dibayar'],
            'tanggal_pembayaran' => Carbon::createFromFormat('d-m-Y H:i', $row['tanggal_pembayaran']),
            'metode_pembayaran' => strtolower($row['metode_pembayaran']),
            'teller_user_id' => $teller?->id,
            'catatan' => $row['catatan'] ?? null,
        ]);
    }
}
