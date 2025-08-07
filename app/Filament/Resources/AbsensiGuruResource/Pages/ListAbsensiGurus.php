<?php

namespace App\Filament\Resources\AbsensiGuruResource\Pages;

use App\Filament\Resources\AbsensiGuruResource;
use App\Exports\AbsensiGuruExport;
use App\Imports\AbsensiGuruImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ListAbsensiGurus extends ListRecords
{
    protected static string $resource = AbsensiGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Export Button
            Actions\Action::make('exportAbsensiGuru')
                ->label('Export Absensi Guru')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    try {
                        return Excel::download(
                            new AbsensiGuruExport,
                            'data-absensi-guru-' . now()->format('YmdHis') . '.xlsx'
                        );
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Export Data Absensi Guru')
                            ->body('Terjadi kesalahan saat mengekspor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),



            Actions\CreateAction::make()->label('Tambah Absensi Guru'),
        ];
    }
}
