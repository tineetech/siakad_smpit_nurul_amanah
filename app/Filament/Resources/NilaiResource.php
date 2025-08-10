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
                Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(Siswa::all()->pluck('nama_lengkap', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                   ->afterStateUpdated(function (Set $set, $state) {
                        $siswa = \App\Models\Siswa::find($state);
                        $set('kelas_id', $siswa?->kelas_id);
                    }),
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->placeholder('Akan terisi otomatis')
                    ->disabled()
                    ->dehydrated(true)
                    ->options(Kelas::all()->pluck('nama', 'id'))
                    ->required(),
                Select::make('semester_id')
                    ->label('Semester')
                    ->options(Semester::all()->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(MataPelajaran::where('jenis', 'kepesantrenan')->get()->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('nilai_harian')
                    ->label('Nilai Harian')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->inputMode('decimal')
                    ->suffix('%') // Opsional: Tambahkan persentase
                    ->step(0.01) // Memungkinkan input desimal dua angka
                    ->nullable(),
                TextInput::make('nilai_pas')
                    ->label('Nilai PAS')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->inputMode('decimal')
                    ->suffix('%')
                    ->step(0.01)
                    ->nullable(),
                TextInput::make('nilai_akhir')
                    ->label('Nilai AKHIR')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->inputMode('decimal')
                    ->suffix('%')
                    ->step(0.01)
                    ->nullable(),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
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
                'edit' => false,
                'delete' => false,
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