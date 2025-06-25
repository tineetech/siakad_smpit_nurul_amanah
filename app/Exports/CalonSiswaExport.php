<?php

namespace App\Exports;

use App\Models\CalonSiswa;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Fill,Color};

class CalonSiswaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $counter=0;
        return CalonSiswa::with('gelombang')->get()->map(fn($c)=>[
            'No'=>++$counter,
            'Gelombang'=>$c->gelombang->nama,
            'Nomor Pendaftaran'=>$c->nomor_pendaftaran,
            'NISN'=>$c->nisn,
            'Nama Lengkap'=>$c->nama_lengkap,
            'Jenis Kelamin'=>$c->jenis_kelamin,
            'TTL'=>($c->tempat_lahir.' '.(Carbon::parse($c->tanggal_lahir)->format('d-m-Y') . '')),
            'Tempat Lahir'=>$c->tempat_lahir,
            'Tanggal Lahir'=>Carbon::parse($c->tanggal_lahir)->format('d-m-Y'),
            'Status'=>$c->status,
            'Tanggal Pendaftaran'=>Carbon::parse($c->tanggal_pendaftaran)->format('d-m-Y H:i'),
        ]);
    }
    public function headings(): array
    {
        return ['No','Gelombang','Nomor Pendaftaran','NISN','Nama Lengkap','Jenis Kelamin','TTL','Tempat Lahir','Tanggal Lahir','Status','Tanggal Pendaftaran'];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->applyFromArray([
            'font'=>['bold'=>true,'color'=>['argb'=>Color::COLOR_WHITE]],
            'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>Color::COLOR_DARKGREEN]],
        ]);
    }
}
