<?php

namespace App\Exports;

use App\Models\Staf; // Import model Staf
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;

class StafExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $dataToExport = Staf::all();
        $counter = 0;

        return $dataToExport->map(function($staf) use (&$counter) {
            $counter++;
            return [
                'No'                => $counter,
                'NIP'               => ' ' . (string) ($staf->nip ?? '-'), // Tambahkan spasi agar dianggap teks di Excel
                'Nama Lengkap'      => $staf->nama_lengkap,
                'Jabatan'           => $staf->jabatan ?? '-',
                'Jenis Kelamin'     => $staf->jenis_kelamin,
                'Tempat Lahir'      => $staf->tempat_lahir ?? '-',
                'Tanggal Lahir'     => $staf->tanggal_lahir ? $staf->tanggal_lahir->format('d-m-Y') : '-',
                'Agama'             => $staf->agama ?? '-',
                'Status'            => $staf->status,
                'Dibuat Pada'       => $staf->created_at->format('d-m-Y H:i:s'),
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
            'NIP',
            'Nama Lengkap',
            'Jabatan',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Status',
            'Dibuat Pada',
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
                    'argb' => Color::COLOR_DARKBLUE, // Warna biru gelap untuk header
                ],
            ],
        ]);
    }
}