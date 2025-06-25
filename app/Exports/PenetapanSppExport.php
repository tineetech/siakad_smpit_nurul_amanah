<?php

namespace App\Exports;

use App\Models\PenetapanSpps;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PenetapanSppExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return PenetapanSpps::with(['siswa', 'pengaturanSpp'])->get()->map(function ($item, $i) {
            return [
                'No' => $i + 1,
                'Siswa' => $item->siswa->nama_lengkap ?? '-',
                'Pengaturan SPP' => $item->pengaturanSpp->nama ?? '-',
                'Status' => ucfirst(str_replace('_', ' ', $item->status)),
                'Jatuh Tempo' => $item->tanggal_jatuh_tempo?->format('d-m-Y') ?? '-',
                'Dibuat Pada' => $item->created_at->format('d-m-Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Siswa',
            'Pengaturan SPP',
            'Status',
            'Jatuh Tempo',
            'Dibuat Pada',
        ];
    }

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

