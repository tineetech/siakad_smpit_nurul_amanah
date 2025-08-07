<?php

namespace App\Filament\Resources\JadwalPelajaranResource\Pages;

use App\Filament\Resources\JadwalPelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select; // Import Select component
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\Semester; // Tetap import Semester

class ListJadwalPelajarans extends ListRecords
{
    protected static string $resource = JadwalPelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // --- Aksi Export Jadwal ---
            Actions\Action::make('exportJadwalPdf')
                ->label('Export Jadwal (PDF)')
                ->icon('heroicon-o-document-arrow-down')
                ->modalHeading('Export Jadwal Pelajaran')
                ->modalSubmitActionLabel('Export')
                ->form([
                    Select::make('kelas_id')
                        ->label('Pilih Kelas')
                        ->options(Kelas::pluck('nama', 'id')->toArray())
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $kelasId = $data['kelas_id'];

                    $kelas = Kelas::find($kelasId);
                    if (!$kelas) {
                        \Filament\Notifications\Notification::make()
                            ->title('Kelas tidak ditemukan.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $query = JadwalPelajaran::where('kelas_id', $kelasId)
                                            ->with(['mataPelajaran', 'guru', 'semester']);

                    $jadwal = $query->get();

                    $semesterObject = null;
                    if ($jadwal->isNotEmpty()) {
                        $semesterObject = $jadwal->first()->semester;
                    }
                    if (!$semesterObject) {
                        $semesterObject = Semester::first(); // Ambil semester pertama sebagai default jika tidak ada jadwal ditemukan
                    }

                    $viewData = [
                        'kelas' => $kelas,
                        'jadwal' => $jadwal,
                        'semester' => $semesterObject,
                    ];
                    // dd($viewData);

                    $pdf = Pdf::loadView('jadwal-pdf', $viewData);

                    return response()->streamDownload(function () use ($pdf, $kelas) {
                        echo $pdf->output();
                    }, 'jadwal-kelas-' . \Illuminate\Support\Str::slug($kelas->nama) . '.pdf');
                }),
            // --- Akhir Aksi Export Jadwal ---
            Actions\CreateAction::make()->label('Tambah Data Jadwal Pelajaran'),
        ];
    }
}