<?php

namespace App\Filament\Resources\AbsensiSiswaResource\Pages;

use App\Filament\Resources\AbsensiSiswaResource;
use App\Exports\AbsensiSiswaExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListAbsensiSiswas extends ListRecords
{
    protected static string $resource = AbsensiSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Export Button
            Actions\Action::make('exportAbsensi')
                ->label('Export Absensi')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    try {
                        return Excel::download(
                            new AbsensiSiswaExport, 
                            'data-absensi-siswa-' . now()->format('YmdHis') . '.xlsx'
                        );
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Export Data Absensi')
                            ->body('Terjadi kesalahan saat mengekspor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}