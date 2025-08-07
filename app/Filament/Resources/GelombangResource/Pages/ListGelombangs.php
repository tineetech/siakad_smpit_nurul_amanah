<?php

namespace App\Filament\Resources\GelombangResource\Pages;

use App\Exports\GelombangExport;
use App\Filament\Resources\GelombangResource;
use App\Imports\GelombangImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class ListGelombangs extends ListRecords
{
    protected static string $resource = GelombangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Export
            Actions\Action::make('exportGelombangPPDB')
                ->label('Export Data')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    try {
                        return Excel::download(new GelombangExport, 'data-gelombangppdb-' . now()->format('YmdHis') . '.xlsx');
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Export Data Guru')
                            ->body('Terjadi kesalahan saat mengekspor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            
            // Tombol Import
            Actions\Action::make('importGelombangPPDB')
                ->label('Import Data')
                ->color('info')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Placeholder::make('download_template_placeholder')
                        ->content(new HtmlString(
                            'Untuk mengimpor data, harap gunakan format file Excel yang benar.<br> ' .
                            'Anda dapat mengunduh <a href="' . asset('templates/template_import_gelombangppdb.xlsx') . '" download class="text-primary-600 hover:text-primary-700 font-semibold underline">contoh file Excel di sini</a>. ' .
                            '<br><br>'
                        ))
                        ->label('')
                        ->columnSpanFull(),

                    FileUpload::make('file')
                        ->label('Pilih File Excel (.xlsx)')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                        ->directory('livewire-tmp') // Simpan di sub-folder sementara di disk default
                        ->disk('local'), // Atau disk yang sesuai (misal: 'public')
                ])
                ->action(function (array $data) {
                    $uploadedFile = $data['file'];
                    try {
                        Excel::import(new GelombangImport, $uploadedFile);
                        Notification::make()
                            ->title('Import Data Berhasil')
                            ->body('Data berhasil diimpor dari file Excel.')
                            ->success()
                            ->send();
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errorMessage = 'Gagal import data gelombang. Terdapat ' . count($failures) . ' baris yang tidak valid: <br>';
                        foreach ($failures as $failure) {
                            $errorMessage .= 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
                        }
                        Notification::make()
                            ->title('Gagal Import Data Gelombang')
                            ->body($errorMessage)
                            ->danger()
                            ->duration(10000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Import Data Gelombang')
                            ->body('Terjadi kesalahan saat mengimpor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Actions\CreateAction::make()->label('Tambah Data Gelombang'),
        ];
    }
}
