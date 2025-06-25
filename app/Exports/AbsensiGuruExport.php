<?php

namespace App\Exports;

use App\Models\AbsensiGuru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Color;

class AbsensiGuruExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Eager load relationships to avoid N+1 queries
        $dataToExport = AbsensiGuru::with(['guru', 'pencatat'])->get();
        $counter = 0;

        return $dataToExport->map(function ($absensi) use (&$counter) {
            $counter++;
            return [
                'No'                => $counter,
                'NIP'               => ' ' . (string) ($absensi->guru->nip ?? '-'),
                'Nama Guru'         => $absensi->guru->nama_lengkap ?? '-',
                'Tanggal Absensi' => Carbon::parse($absensi->tanggal_absensi)->format('d-m-Y'),
                'Waktu Absensi'     => $absensi->waktu_absensi,
                'Status Kehadiran' => $this->getStatusText($absensi->status_kehadiran),
                'Mode Absensi'     => $this->getModeText($absensi->mode_absensi),
                'Pencatat'         => $absensi->pencatat->name ?? '-',
                'QR Code Terscan'  => $absensi->qr_code_terscan,
                'Catatan'          => $absensi->catatan ?? '-',
                'Dibuat Pada'      => $absensi->created_at->format('d-m-Y H:i:s'),
            ];
        });
    }

    /**
     * Convert status code to text
     */
    protected function getStatusText($status)
    {
        $statuses = [
            AbsensiGuru::STATUS_HADIR => 'Hadir',
            AbsensiGuru::STATUS_TERLAMBAT => 'Terlambat',
            AbsensiGuru::STATUS_IZIN => 'Izin',
            AbsensiGuru::STATUS_SAKIT => 'Sakit',
            AbsensiGuru::STATUS_ALPA => 'Alpa',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Convert mode absensi to text
     */
    protected function getModeText($mode)
    {
        $modes = [
            AbsensiGuru::MODE_QR => 'Scan QR',
            AbsensiGuru::MODE_MANUAL => 'Manual',
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
            'NIP',
            'Nama Guru',
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

        // Optional: Add alternating row colors
        $sheet->getStyle('A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => Color::COLOR_YELLOW,
                    ],
                ],
                'font' => [
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ]);
    }
}
