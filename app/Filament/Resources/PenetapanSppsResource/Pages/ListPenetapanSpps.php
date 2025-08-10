<?php
namespace App\Filament\Resources\PenetapanSppsResource\Pages;

use App\Filament\Resources\PenetapanSppsResource;
use App\Exports\PenetapanSppExport;
use App\Imports\PenetapanSppImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ListPenetapanSpps extends ListRecords
{
    protected static string $resource = PenetapanSppsResource::class;

    public function getTitle(): string | Htmlable
    {
        if (Auth::user()->role === 'siswa') {
            return 'Tagihan';
        }
        return 'Penetapan Pembayaran';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn ($record) => Auth::user()->role === 'admin')
                ->action(fn () => Excel::download(new PenetapanSppExport, 'penetapan_spp.xlsx')),

            // Actions\Action::make('import')
            //     ->label('Import')
            //     ->icon('heroicon-o-arrow-up-tray')
            //     ->color('info')
            //     ->form([
            //         Placeholder::make('note')->content(
            //             new HtmlString('Gunakan template Excel yang sesuai. <br>
            //                 <a href="' . asset('templates/template_penetapan_spp.xlsx') . '" download class="text-primary-600 underline">Unduh Template</a>')
            //         ),
            //         FileUpload::make('file')
            //             ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
            //             ->required()
            //             ->directory('livewire-tmp'),
            //     ])
            //     ->action(function (array $data) {
            //         try {
            //             Excel::import(new PenetapanSppImport, $data['file']);
            //             Notification::make()->title('Berhasil')->body('Data berhasil diimpor.')->success()->send();
            //         } catch (\Exception $e) {
            //             Notification::make()->title('Gagal Import')->body($e->getMessage())->danger()->send();
            //         }
            //     }),

            Actions\CreateAction::make()->label('Tambah Data Penetapan Pembayaran'),
        ];
    }
}

