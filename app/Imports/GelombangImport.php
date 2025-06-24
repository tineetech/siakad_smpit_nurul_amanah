<?php

namespace App\Imports;

use App\Models\Gelombang;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Validation\Rule;


class GelombangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Gelombang([
            'nama'=>$row['nama'] ?? "Unknown",
            'kouta'=>$row['kouta'] ?? 0,
            'kouta_terisi'=>$row['kouta_terisi'] ?? 0,
            'tanggal_mulai'=>\Carbon\Carbon::createFromFormat('d-m-Y',$row['tanggal_mulai']) ?? null,
            'tanggal_berakhir'=>\Carbon\Carbon::createFromFormat('d-m-Y',$row['tanggal_berakhir']) ?? null,
            'created_by'=> User::where('name', $row['created_by'])->first()->id ?? Auth::user()->name,
        ]);
    }
    public function rules(): array
    {
        return [
            'nama'=>'required|string|max:255',
            'kouta'=>'nullable|integer',
            'tanggal_mulai'=>'required|date_format:d-m-Y',
            'tanggal_berakhir'=>'required|date_format:d-m-Y',
        ];
    }
}

