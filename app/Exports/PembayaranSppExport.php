<?php

namespace App\Exports;

use App\Models\PembayaranSpp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class PembayaranSppExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $data = PembayaranSpp::with(['siswa', 'teller'])->get();
        $counter = 1;

        return $data->map(function ($item) use (&$counter) {
            return [
                'No' => $counter++,
                'Nama Siswa' => $item->siswa->nama_lengkap ?? '-',
                'Jumlah Dibayar' => "Rp" . (string) number_format((float) $item->jumlah_dibayar),
                'Tanggal Pembayaran' => Carbon::parse($item->tanggal_pembayaran)->format('d-m-Y'),
                'Metode Pembayaran' => $item->metode_pembayaran,
                'Status TRX' => $item->penetapan->status,
                'Teller' => $item->teller->name ?? '-',
                'Catatan' => $item->catatan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Jumlah Dibayar',
            'Tanggal Pembayaran',
            'Metode Pembayaran',
            'Status TRX',
            'Teller',
            'Catatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => Color::COLOR_DARKGREEN]],
        ]);
    }
}
