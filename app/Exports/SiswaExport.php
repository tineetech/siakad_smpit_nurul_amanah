<?php

namespace App\Exports;

use App\Models\Siswa; // Import model Siswa
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;

class SiswaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Eager load relasi 'kelas' untuk menghindari N+1 query
        $dataToExport = Siswa::with('kelas')->get();
        $counter = 0;

        return $dataToExport->map(function($siswa) use (&$counter) {
            $counter++;
            return [
                'No'                => $counter,
                'Nama Lengkap'      => $siswa->nama_lengkap,
                'NISN'              => ' ' . (string) $siswa->nisn, // Tambahkan spasi agar dianggap teks
                'NIS'               => $siswa->nis ? ' ' . (string) $siswa->nis : '-', // Jika NIS ada, tambahkan spasi, jika tidak tampilkan '-'
                'Jenis Kelamin'     => $siswa->jenis_kelamin,
                'Tempat Lahir'      => $siswa->tempat_lahir,
                'Tanggal Lahir'     => $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d-m-Y') : '-', // Format tanggal
                'Agama'             => $siswa->agama,
                'Nama Ayah'         => $siswa->nama_ayah ?? '-',
                'Nama Ibu'          => $siswa->nama_ibu ?? '-',
                'No. HP Orang Tua'  => $siswa->nomor_telepon_orang_tua ?? '-',
                'Kelas'             => $siswa->kelas->nama ?? '-', // Ambil nama kelas dari relasi
                'Status Siswa'      => $siswa->status,
                'Dibuat Pada'       => $siswa->created_at->format('d-m-Y H:i:s'),
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
            'Nama Lengkap',
            'NISN',
            'NIS',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Nama Ayah',
            'Nama Ibu',
            'No. HP Orang Tua',
            'Kelas',
            'Status Siswa',
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