<?php

namespace App\Exports;

use App\Models\PengaturanSpp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PengaturanSppExport implements  FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return PengaturanSpp::all();
        $dataToExport = PengaturanSpp::all();
        $counter = 0;

        return $dataToExport->map(function($spp) use (&$counter) {
            $counter++;
            return [
                'No'                 => $counter,
                'Nama Pengaturan SPP'=> ' ' . (string) ($spp->nama ?? '-'), // Tambahkan spasi agar dianggap teks
                'Jumlah'             => $spp->jumlah,
                'Tanggal Mulai'      => $spp->tanggal_mulai,
                'Tanggal Berakhir'      => $spp->tanggal_berakhir,
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
            'Nama Pengaturan SPP',
            'Jumlah',
            'Tanggal Mulai',
            'Tanggal Berakhir',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Memberi warna latar belakang pada baris header (baris 1)
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE], // Warna teks putih
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => Color::COLOR_DARKGREEN, // Warna hijau gelap untuk header
                ],
            ],
        ]);
    }
}