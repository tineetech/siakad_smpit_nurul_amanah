<?php

namespace App\Imports;

use App\Models\PengaturanSpp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class PengaturanSppImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $pengaturanSpp = PengaturanSpp::find($row['id'] ?? null);

        if ($pengaturanSpp) {
            $pengaturanSpp->update([
                'nama' => $row['nama_pengaturan_spp'],
                'jumlah' => $row['jumlah'],
                'tanggal_mulai' => Carbon::parse($row['tanggal_mulai']),
                'tanggal_berakhir' => Carbon::parse($row['tanggal_berakhir']),
            ]);
            return $pengaturanSpp;
        }

        return new PengaturanSpp([
            'nama' => $row['nama_pengaturan_spp'],
            'jumlah' => $row['jumlah'],
            'tanggal_mulai' => Carbon::parse($row['tanggal_mulai']),
            'tanggal_berakhir' => Carbon::parse($row['tanggal_berakhir']),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pengaturan_spp' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_pengaturan_spp.required' => 'Kolom "Nama Pengaturan SPP" wajib diisi.',
            'jumlah.required' => 'Kolom "Jumlah" wajib diisi.',
            'jumlah.numeric' => 'Kolom "Jumlah" harus berupa angka.',
            'tanggal_mulai.required' => 'Kolom "Tanggal Mulai" wajib diisi.',
            'tanggal_mulai.date' => 'Kolom "Tanggal Mulai" harus format tanggal yang valid.',
            'tanggal_berakhir.required' => 'Kolom "Tanggal Berakhir" wajib diisi.',
            'tanggal_berakhir.date' => 'Kolom "Tanggal Berakhir" harus format tanggal yang valid.',
            'tanggal_berakhir.after_or_equal' => 'Kolom "Tanggal Berakhir" harus setelah atau sama dengan "Tanggal Mulai".',
        ];
    }
}