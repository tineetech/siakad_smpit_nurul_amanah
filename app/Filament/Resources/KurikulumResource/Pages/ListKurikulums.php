<?php

namespace App\Filament\Resources\KurikulumResource\Pages;

use App\Filament\Resources\KurikulumResource;
use App\Exports\KurikulumExport;
use App\Imports\KurikulumImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ListKurikulums extends ListRecords
{
    protected static string $resource = KurikulumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Export
            Actions\Action::make('exportKurikulum')
                ->label('Export Kurikulum')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    try {
                        return Excel::download(new KurikulumExport, 'data-kurikulum-' . now()->format('YmdHis') . '.xlsx');
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Export Data Kurikulum')
                            ->body('Terjadi kesalahan saat mengekspor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // Tombol Import
            Actions\Action::make('importKurikulum')
                ->label('Import Kurikulum')
                ->color('info')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Placeholder::make('download_template_placeholder')
                        ->content(new HtmlString(
                            'Untuk mengimpor data, harap gunakan format file Excel yang benar.<br> ' .
                            'Anda dapat mengunduh <a href="' . asset('templates/template_import_kurikulum.xlsx') . '" download class="text-primary-600 hover:text-primary-700 font-semibold underline">contoh file Excel di sini</a>. ' .
                            '<br><br>'
                        ))
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
                        Excel::import(new KurikulumImport, $uploadedFile);
                        Notification::make()
                            ->title('Import Data Kurikulum Berhasil')
                            ->body('Data kurikulum berhasil diimpor dari file Excel.')
                            ->success()
                            ->send();
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errorMessage = 'Gagal import data kurikulum. Terdapat ' . count($failures) . ' baris yang tidak valid: <br>';
                        foreach ($failures as $failure) {
                            $errorMessage .= 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
                        }
                        Notification::make()
                            ->title('Gagal Import Data Kurikulum')
                            ->body($errorMessage)
                            ->danger()
                            ->duration(10000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Import Data Kurikulum')
                            ->body('Terjadi kesalahan saat mengimpor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()->label('Tambah Data Kurikulum'),
        ];
    }
}