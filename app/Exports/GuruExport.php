<?php

namespace App\Exports;

use App\Models\Guru; // Import model Guru
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;

class GuruExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Eager load relasi 'mataPelajaran' dan 'kelas' untuk menghindari N+1 query
        $dataToExport = Guru::with(['mataPelajaran', 'kelas'])->get();
        $counter = 0;

        return $dataToExport->map(function($guru) use (&$counter) {
            $counter++;
            return [
                'No'                => $counter,
                'NIY'               => ' ' . (string) ($guru->niy ?? '-'), // Tambahkan spasi agar dianggap teks
                'Nama Lengkap'      => $guru->nama_lengkap,
                'Jenis Kelamin'     => $guru->jenis_kelamin,
                'Tempat Lahir'      => $guru->tempat_lahir ?? '-',
                'Tanggal Lahir'     => $guru->tanggal_lahir ? $guru->tanggal_lahir->format('d-m-Y') : '-',
                'Agama'             => $guru->agama ?? '-',
                'Mata Pelajaran Diampu' => $guru->mataPelajaran->nama ?? '-', // Ambil nama mata pelajaran dari relasi
                'Wali Kelas'        => $guru->kelas->nama ?? '-', // Ambil nama kelas dari relasi (jika wali kelas)
                'Status'            => $guru->status,
                'Dibuat Pada'       => $guru->created_at->format('d-m-Y H:i:s'),
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
            'NIY',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Mata Pelajaran Diampu',
            'Wali Kelas',
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
                    'argb' => Color::COLOR_DARKGREEN, // Warna hijau gelap untuk header
                ],
            ],
        ]);
    }
}