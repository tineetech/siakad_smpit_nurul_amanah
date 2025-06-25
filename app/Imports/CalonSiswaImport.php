<?php

namespace App\Imports;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class CalonSiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $gel = Gelombang::where('nama',$row['gelombang'])->first();
        return new CalonSiswa([
            'gelombang_id'=>$gel?->id,
            'nomor_pendaftaran'=>$row['nomor_pendaftaran'],
            'nisn'=>$row['nisn']?:null,
            'nama_lengkap'=>$row['nama_lengkap'],
            'jenis_kelamin'=>$row['jenis_kelamin'],
            'tempat_lahir'=>$row['tempat_lahir'],
            'tanggal_lahir'=>\Carbon\Carbon::createFromFormat('d-m-Y',$row['tanggal_lahir']),
            'status'=>$row['status'],
            'tanggal_pendaftaran'=>\Carbon\Carbon::createFromFormat('d-m-Y H:i',$row['tanggal_pendaftaran']),
        ]);
    }
    public function rules(): array
    {
        return [
            'nomor_pendaftaran'=>'required|unique:calon_siswa,nomor_pendaftaran',
            'gelombang'=>'required|exists:gelombang,nama',
            'tanggal_lahir'=>'nullable|date_format:d-m-Y',
            'tanggal_pendaftaran'=>'required|date_format:d-m-Y H:i',
        ];
    }
}
