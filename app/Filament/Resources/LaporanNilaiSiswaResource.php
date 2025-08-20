<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanNilaiSiswaResource\Pages;
use App\Models\Guru;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

class LaporanNilaiSiswaResource extends Resource
{
    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Kesiswaan'; 
    protected static ?string $navigationLabel = 'Raport';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Field form bisa kosong atau tidak digunakan jika resource ini hanya untuk melihat laporan
                // Namun, kita bisa tambahkan filter di bagian tabel
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('semester.nama')
                    ->label('Semester')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
            
                TextColumn::make('nilai_akhir')
                    ->label('Nilai Akhir')
                    ->numeric(),

                TextColumn::make('nilai_kkm')
                    ->label('Nilai KKM')
                    ->numeric()
            ])
            ->filters([
                SelectFilter::make('siswa_id')
                    ->label('Filter Siswa')
                    ->options(Siswa::all()->pluck('nama_lengkap', 'id'))
                    ->searchable(),
                SelectFilter::make('semester_id')
                    ->label('Filter Semester')
                    ->options(Semester::all()->pluck('nama', 'id'))
                    ->searchable(),
                SelectFilter::make('kelas_id')
                    ->label('Filter Kelas')
                    ->options(function () {
                        // Pastikan model Kelas ada dan memiliki kolom 'nama_kelas'
                        return \App\Models\Kelas::all()->pluck('nama', 'id');
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('export_raport')
                    ->label('Export PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Model $record) => route('export.raport', [
                        'siswa_id' => $record->siswa_id,
                        'semester_id' => $record->semester_id,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                //
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->select('siswa_id', 'semester_id', 'kelas_id')
                    ->selectRaw('AVG(nilai_akhir) as nilai_akhir')
                    ->selectRaw('AVG(nilai_kkm) as nilai_kkm')
                    ->addSelect(\DB::raw('MIN(id) as id'))
                    ->groupBy('siswa_id', 'semester_id', 'kelas_id');
            })
            ->defaultSort('siswa.nama_lengkap', 'asc');
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        // $user = Auth::user();
    
        // if ($user->isGuru()) {
        //     $guru = Guru::where('user_id', $user->id)->first();
        //     if (empty($guru->kelas_id)) {
        //         return false;
        //     }
        //     return true;
        // }
        return true;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanNilaiSiswas::route('/'),
        ];
    }

    // Metode ini penting untuk preload relasi agar tidak N+1 query
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['siswa', 'semester', 'kelas']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSiswa()) {
            $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();

            if ($siswa) {
                $query->where('siswa_id', $siswa->id);
            } else {
                // Jika tidak ada data siswa, kembalikan query kosong
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    // Metode ini untuk menonaktifkan tombol buat baru di halaman list jika tidak diperlukan
    public static function canCreate(): bool
    {
        return false;
    }
}