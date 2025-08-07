<?php

namespace App\Filament\Resources\SemesterResource\Pages;

use App\Filament\Resources\SemesterResource;
use App\Exports\SemesterExport;
use App\Imports\SemesterImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ListSemesters extends ListRecords
{
    protected static string $resource = SemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Export
            Actions\Action::make('exportSemester')
                ->label('Export Semester')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    try {
                        return Excel::download(new SemesterExport, 'data-semester-' . now()->format('YmdHis') . '.xlsx');
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Export Data Semester')
                            ->body('Terjadi kesalahan saat mengekspor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // Tombol Import
            Actions\Action::make('importSemester')
                ->label('Import Semester')
                ->color('info')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Placeholder::make('download_template_placeholder')
                        ->content(new HtmlString(
                            'Untuk mengimpor data, harap gunakan format file Excel yang benar.<br> ' .
                            'Anda dapat mengunduh <a href="' . asset('templates/template_import_semester.xlsx') . '" download class="text-primary-600 hover:text-primary-700 font-semibold underline">contoh file Excel di sini</a>. ' .
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
                        Excel::import(new SemesterImport, $uploadedFile);
                        Notification::make()
                            ->title('Import Data Semester Berhasil')
                            ->body('Data semester berhasil diimpor dari file Excel.')
                            ->success()
                            ->send();
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errorMessage = 'Gagal import data semester. Terdapat ' . count($failures) . ' baris yang tidak valid: <br>';
                        foreach ($failures as $failure) {
                            $errorMessage .= 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
                        }
                        Notification::make()
                            ->title('Gagal Import Data Semester')
                            ->body($errorMessage)
                            ->danger()
                            ->duration(10000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Import Data Semester')
                            ->body('Terjadi kesalahan saat mengimpor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()->label('Tambah Data Semester'),
        ];
    }
}