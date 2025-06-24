<?php

namespace App\Exports;

use App\Models\Gelombang;
use App\Models\Kurikulum;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class GelombangExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $counter=0;
        return Gelombang::all()->map(fn($g)=>[
            'No' => ++$counter,
            'Nama' => $g->nama,
            'Kuota' => $g->kouta,
            'Kouta Terisi' => $g->kouta_terisi,
            'Tanggal Mulai' => Carbon::parse($g->tanggal_mulai)->format('d-m-Y'),
            'Tanggal Berakhir' => Carbon::parse($g->tanggal_berakhir)->format('d-m-Y'),
            'Created by' => User::where('id', $g->created_by)->first()->name ?? '-',
        ]);
    }
    public function headings(): array
    {
        return ['No','Nama','Kuota','Terisi','Mulai','Berakhir','Pembuat'];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->applyFromArray([
            'font'=>['bold'=>true,'color'=>['argb'=>Color::COLOR_WHITE]],
            'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>Color::COLOR_DARKGREEN]],
        ]);
    }
}
