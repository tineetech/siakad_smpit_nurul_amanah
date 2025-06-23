<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Filament\Resources\KelasResource;
use App\Exports\KelasExport;
use App\Imports\KelasImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Maatwebsite\Excel\Facades\Excel;

class ListKelas extends ListRecords
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Export
            Actions\Action::make('exportKelas')
                ->label('Export Kelas')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    try {
                        return Excel::download(new KelasExport, 'data-kelas-' . now()->format('YmdHis') . '.xlsx');
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Export Data Kelas')
                            ->body('Terjadi kesalahan saat mengekspor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // Tombol Import
            Actions\Action::make('importKelas')
                ->label('Import Kelas')
                ->color('info')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Placeholder::make('download_template_placeholder')
                        ->content(
                            'Untuk mengimpor data, harap gunakan format file Excel yang benar.<br> ' .
                            'Anda dapat mengunduh <a href="' . asset('templates/template_import_kelas.xlsx') . '" download class="text-primary-600 hover:text-primary-700 font-semibold underline">contoh file Excel di sini</a>. ' .
                            '<br><br>'
                        )
                        ->label('')
                        ->columnSpanFull(),

                    FileUpload::make('file')
                        ->label('Pilih File Excel (.xlsx)')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                        ->directory('livewire-tmp')
                        ->disk('local'),
                ])
                ->action(function (array $data) {
                    $uploadedFile = $data['file'];
                    try {
                        Excel::import(new KelasImport, $uploadedFile);
                        Notification::make()
                            ->title('Import Data Kelas Berhasil')
                            ->body('Data kelas berhasil diimpor dari file Excel.')
                            ->success()
                            ->send();
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errorMessage = 'Gagal import data kelas. Terdapat ' . count($failures) . ' baris yang tidak valid: <br>';
                        foreach ($failures as $failure) {
                            $errorMessage .= 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
                        }
                        Notification::make()
                            ->title('Gagal Import Data Kelas')
                            ->body($errorMessage)
                            ->danger()
                            ->duration(10000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Import Data Kelas')
                            ->body('Terjadi kesalahan saat mengimpor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}