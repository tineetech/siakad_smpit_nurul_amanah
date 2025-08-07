<?php

namespace App\Filament\Resources\PembayaranSppResource\Pages;

use App\Filament\Resources\PembayaranSppResource;
use App\Imports\PembayaranSppImport;
use App\Exports\PembayaranSppExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListPembayaranSpps extends ListRecords
{
    protected static string $resource = PembayaranSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Export Transaksi')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => Excel::download(new PembayaranSppExport, 'pembayaran_spp.xlsx')),

            // Actions\Action::make('Import Transaksi')
            //     ->color('info')
            //     ->icon('heroicon-o-arrow-up-tray')
            //     ->form([
            //         \Filament\Forms\Components\FileUpload::make('file')
            //             ->label('Pilih file Excel')
            //             ->required()
            //             ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),
            //     ])
            //     ->action(function (array $data) {
            //         Excel::import(new PembayaranSppImport, $data['file']);
            //         $this->notify('success', 'Import berhasil!');
            //     }),
            
            Actions\CreateAction::make()->label('Tambah Data Pembayaran SPP'),

        ];
    }
}
