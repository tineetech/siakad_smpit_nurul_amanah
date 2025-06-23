<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class KelasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $counter = 0;
        // Eager load relasi waliKelas untuk mendapatkan nama guru
        return Kelas::with('waliKelas')->get()->map(function($kelas) use (&$counter) {
            $counter++;
            return [
                'No'            => $counter,
                'Nama Kelas'    => $kelas->nama,
                'Tingkat'       => $kelas->tingkat ?? '-',
                'Kapasitas'     => $kelas->kapasitas,
                'Wali Kelas'    => $kelas->waliKelas->nama_lengkap ?? '-', // Nama wali kelas
                'Dibuat Pada'   => $kelas->created_at->format('d-m-Y H:i:s'),
            ];
        });
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'No',
            'Nama Kelas',
            'Tingkat',
            'Kapasitas',
            'Wali Kelas',
            'Dibuat Pada',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => Color::COLOR_DARKBLUE,
                ],
            ],
        ]);
    }
}