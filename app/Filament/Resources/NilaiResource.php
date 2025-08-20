<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiResource\Pages;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class NilaiResource extends Resource
{
    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Kesiswaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('semester_id')
            ->label('Semester')
            ->options(Semester::pluck('nama', 'id'))
            ->required()
            ->reactive()
            ->afterStateUpdated(function (Set $set, $state, callable $get) {
                $semesterId = $get('semester_id');
                if (!$semesterId || !$state) {
                    $set('mapel_nilai', []);
                    return;
                }

                $siswa = Siswa::find($get('siswa_id'));
                if (!$siswa) {
                    $set('mapel_nilai', []);
                    return;
                }

                // Ambil semua mapel dari jadwal kelas siswa
                $mapelList = \App\Models\JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
                    // ->where('semester_id', $semesterId)
                    ->with('mataPelajaran')
                    ->get();

                // Ambil mapel yang sudah punya nilai
                $mapelSudahAda = \App\Models\Nilai::where('siswa_id', $siswa->id)
                    ->where('semester_id', $semesterId)
                    ->pluck('mata_pelajaran_id')
                    ->toArray();

                // Filter mapel yang belum ada nilainya
                $mapelBelumAda = $mapelList->filter(function ($jadwal) use ($mapelSudahAda) {
                    return !in_array($jadwal->mata_pelajaran_id, $mapelSudahAda);
                });

                // Set repeater data awal
                $set('mapel_nilai', $mapelBelumAda->map(function ($jadwal) {
                    return [
                        'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                        'mata_pelajaran_nama' => $jadwal->mataPelajaran->nama,
                    ];
                })->values()->toArray());
            }),

        Select::make('siswa_id')
            ->label('Siswa')
            ->options(Siswa::pluck('nama_lengkap', 'id'))
            ->required()
            ->reactive()
            ->afterStateUpdated(function (Set $set, $state, callable $get) {
                $semesterId = $get('semester_id');
                if (!$semesterId || !$state) {
                    $set('mapel_nilai', []);
                    return;
                }

                $siswa = Siswa::find($state);
                if (!$siswa) {
                    $set('mapel_nilai', []);
                    return;
                }

                // Ambil semua mapel dari jadwal kelas siswa
                $mapelList = \App\Models\JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
                    // ->where('semester_id', $semesterId)
                    ->with('mataPelajaran')
                    ->get();

                // Ambil mapel yang sudah punya nilai
                $mapelSudahAda = \App\Models\Nilai::where('siswa_id', $siswa->id)
                    ->where('semester_id', $semesterId)
                    ->pluck('mata_pelajaran_id')
                    ->toArray();

                // Filter mapel yang belum ada nilainya
                $mapelBelumAda = $mapelList->filter(function ($jadwal) use ($mapelSudahAda) {
                    return !in_array($jadwal->mata_pelajaran_id, $mapelSudahAda);
                });

                // Set repeater data awal
                $set('mapel_nilai', $mapelBelumAda->map(function ($jadwal) {
                    return [
                        'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                        'mata_pelajaran_nama' => $jadwal->mataPelajaran->nama,
                    ];
                })->values()->toArray());
            }),

            Forms\Components\Repeater::make('mapel_nilai')
            ->label('Nilai Mapel')
            ->schema([
                Forms\Components\TextInput::make('mata_pelajaran_nama')
                    ->label('Mata Pelajaran')
                    ->disabled()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('nilai_harian')
                    ->numeric()->minValue(1)->maxValue(100)
                    ->columnSpan(1),
                Forms\Components\TextInput::make('nilai_pas')
                    ->numeric()->minValue(1)->maxValue(100)
                    ->columnSpan(1),
                Forms\Components\TextInput::make('nilai_akhir')
                    ->numeric()->minValue(1)->maxValue(100)
                    ->columnSpan(1),
                Forms\Components\TextInput::make('nilai_kkm')
                    ->numeric()->minValue(1)->maxValue(100)
                    ->columnSpan(1),
                Forms\Components\TextInput::make('keterangan')
                    ->nullable()
                    ->columnSpan(2),
            ])
            ->deletable(false)
            ->columns(8)
            ->columnSpanFull()
            ->afterStateHydrated(function ($component, $state, $record) {
                if (!$record) return; // hanya untuk edit

                $siswa = $record->siswa;
                $semesterId = $record->semester_id;

                // Ambil semua mapel dari jadwal kelas
                $mapelList = \App\Models\JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
                    // ->where('semester_id', $semesterId)
                    ->with('mataPelajaran')
                    ->get();

                $data = $mapelList->map(function ($jadwal) use ($siswa, $semesterId) {
                    $nilai = \App\Models\Nilai::where('siswa_id', $siswa->id)
                        ->where('semester_id', $semesterId)
                        ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                        ->first();

                    return [
                        'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                        'mata_pelajaran_nama' => $jadwal->mataPelajaran->nama,
                        'nilai_harian' => (int) $nilai?->nilai_harian,
                        'nilai_pas' => (int) $nilai?->nilai_pas,
                        'nilai_akhir' => (int) $nilai?->nilai_akhir,
                        'nilai_kkm' => (int) $nilai?->nilai_kkm,
                        'keterangan' => $nilai?->keterangan,
                    ];
                })->toArray();

                $component->state($data);
            })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('semester.nama')
                    ->label('Semester')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->default('-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nilai_harian')
                    ->label('Nilai Harian')
                    ->formatStateUsing(fn (string $state): string => (int) "{$state}"), // Menambahkan 
                TextColumn::make('nilai_pas')
                    ->label('Nilai PAS')
                    ->formatStateUsing(fn (string $state): string => (int) "{$state}"),
                TextColumn::make('nilai_akhir')
                    ->label('Nilai Akhir')
                    ->formatStateUsing(fn (string $state): string => (int) "{$state}"),
                TextColumn::make('nilai_kkm')
                    ->label('Nilai KKM')
                    ->formatStateUsing(fn (string $state): string => (int) "{$state}"),
                TextColumn::make('keterangan')
                    ->label('Keterangan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->options(Semester::all()->pluck('nama', 'id')),

                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->options(Kelas::all()->pluck('nama', 'id')),

                Tables\Filters\SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(MataPelajaran::where('jenis', 'kepesantrenan')->pluck('nama', 'id')),

                // Tables\Filters\SelectFilter::make('siswa_id')
                //     ->label('Siswa')
                //     ->options(Siswa::all()->pluck('nama_lengkap', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListNilais::route('/'),
            'create' => Pages\CreateNilai::route('/create'),
            'edit' => Pages\EditNilai::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        // $user = Auth::user();
    
        // if ($user->isGuru()) {
        //     $guru = Guru::where('user_id', $user->id)->first();
        //     if (!$guru->kelas_id) {
        //         return false;
        //     }
        //     return true;
        // }
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return $query->where('id', 0);
        }

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($guru) {
                    return $query->where('kelas_id', $guru->kelas_id);
                }
            return $query->where('id', 0);
        }
            
        if ($user->isSiswa()) {
            $siswa = Siswa::where('user_id', $user->id)->first();
            if ($siswa) {
                return $query->where('siswa_id', $siswa->id);
            }
            return $query->where('id', 0);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return self::getCurrentUserRolePermissions('viewAny');
    }

    public static function canCreate(): bool
    {
        return self::getCurrentUserRolePermissions('create');
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            return $guru && $record->guru_id === $guru->id && $record->kelas_id === $guru->kelas_id;
        }

        return self::getCurrentUserRolePermissions('edit');
    }

    public static function canDelete(Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            return $guru && $record->guru_id === $guru->id && $record->kelas_id === $guru->kelas_id;
        }

        return self::getCurrentUserRolePermissions('delete');
    }

    public static function canDeleteAny(): bool
    {
        return self::getCurrentUserRolePermissions('delete');
    }

    protected static function getCurrentUserRolePermissions(string $action): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $rolePermissions = [
            User::ROLE_ADMIN => [
                'viewAny' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
            User::ROLE_TATA_USAHA => [
                'viewAny' => false,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
            User::ROLE_GURU => [
                'viewAny' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
            User::ROLE_SISWA => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }
}