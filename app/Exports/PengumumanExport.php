<?php

namespace App\Exports;

use App\Models\Pengumuman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PengumumanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $counter = 0;
        // Eager load relasi dipostingOleh untuk mendapatkan nama user
        return Pengumuman::with('dipostingOleh')->get()->map(function($p) use (&$counter) {
            $counter++;
            return [
                'No'                    => $counter,
                'Judul'                 => $p->judul,
                'Konten'                => $p->konten,
                'Diposting Oleh'        => $p->dipostingOleh->name ?? '-', // Ambil nama user
                'Target Peran'          => $p->target_peran ?? '-',
                'Tanggal Publikasi'     => $p->tanggal_publikasi ? $p->tanggal_publikasi->format('d-m-Y H:i:s') : '-',
                'Dibuat Pada'           => $p->created_at->format('d-m-Y H:i:s'),
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
            'Judul',
            'Konten',
            'Diposting Oleh',
            'Target Peran',
            'Tanggal Publikasi',
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
                    'argb' => Color::COLOR_MAGENTA,
                ],
            ],
        ]);
    }
}