<?php

namespace App\Exports;

use App\Models\Kurikulum;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class KurikulumExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $counter = 0;
        return Kurikulum::all()->map(function($kurikulum) use (&$counter) {
            $counter++;
            return [
                'No'                => $counter,
                'Nama Kurikulum'    => $kurikulum->nama,
                'Deskripsi'         => $kurikulum->deskripsi ?? '-',
                'Tahun Mulai'       => $kurikulum->tahun_mulai ?? '-',
                'Tahun Berakhir'    => $kurikulum->tahun_berakhir ?? '-',
                'Aktif'             => $kurikulum->is_aktif ? 'Ya' : 'Tidak',
                'Dibuat Pada'       => $kurikulum->created_at->format('d-m-Y H:i:s'),
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
            'Nama Kurikulum',
            'Deskripsi',
            'Tahun Mulai',
            'Tahun Berakhir',
            'Aktif',
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
                    'argb' => Color::COLOR_DARKGREEN,
                ],
            ],
        ]);
    }
}