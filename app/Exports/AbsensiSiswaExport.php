<?php

namespace App\Exports;

use App\Models\AbsensiSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;



class AbsensiSiswaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Eager load relationships to avoid N+1 queries
        $dataToExport = AbsensiSiswa::with(['siswa', 'pencatat'])->get();
        $counter = 0;

        return $dataToExport->map(function ($absensi) use (&$counter) {
            $counter++;
            return [
                'No'                => $counter,
                'NIS'               => ' ' . (string) ($absensi->siswa->nis ?? '-'),
                'Nama Siswa'        => $absensi->siswa->nama_lengkap ?? '-',
                'Tanggal Absensi' => Carbon::parse($absensi->tanggal_absensi)->format('d-m-Y'),

                'Waktu Absensi'     => $absensi->waktu_absensi,
                'Status Kehadiran' => $this->getStatusText($absensi->status_kehadiran),
                'Mode Absensi'     => $this->getModeText($absensi->mode_absensi),
                'Pencatat'         => $absensi->pencatat->name ?? '-',
                'QR Code Terscan'  => $absensi->qr_code_terscan,
                'Catatan'         => $absensi->catatan ?? '-',
                'Dibuat Pada'     => $absensi->created_at->format('d-m-Y H:i:s'),
            ];
        });
    }

    /**
     * Convert status code to text
     */
    protected function getStatusText($status)
    {
        $statuses = [
            'H' => 'Hadir',
            'I' => 'Izin',
            'S' => 'Sakit',
            'A' => 'Alpa',
            'T' => 'Terlambat',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Convert mode absensi to text
     */
    protected function getModeText($mode)
    {
        $modes = [
            'manual' => 'Manual',
            'qr' => 'QR Code',
            'rfid' => 'RFID',
        ];

        return $modes[$mode] ?? $mode;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Tanggal Absensi',
            'Waktu Absensi',
            'Status Kehadiran',
            'Mode Absensi',
            'Pencatat',
            'QR Code Terscan',
            'Catatan',
            'Dibuat Pada',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style for header row
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

        // Auto-size all columns
        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
