<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalPelajaranResource\Pages;
use App\Filament\Resources\JadwalPelajaranResource\RelationManagers;
use App\Models\JadwalPelajaran;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class JadwalPelajaranResource extends Resource
{
    protected static ?string $model = JadwalPelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Jadwal Pelajaran';
    protected static ?string $navigationGroup = 'Kesiswaan';
    public static function form(Form $form): Form
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isGuru = $user && $user->isGuru();
        $guruLogin = $isGuru ? Guru::where('user_id', $user->id)->first() : null;

        return $form
            ->schema([
                // Field Kelas untuk Guru (hidden)
                Forms\Components\Hidden::make('kelas_id')
                    ->default($guruLogin->kelas_id ?? null)
                    ->required(),

                // Field Kelas untuk Admin/TU (visible)
                Forms\Components\Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->required()
                    ->hidden($isGuru) // Sembunyikan untuk guru
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('guru_id')
                    ->label('Guru')
                    ->options(Guru::query()
                        ->with('mataPelajaran')
                        ->get()
                        ->pluck('nama_lengkap', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $guru = Guru::with('mataPelajaran')->find($state);
                            if ($guru && $guru->mataPelajaran) {
                                $set('mata_pelajaran_id', $guru->mata_pelajaran_id);
                            }
                        }
                    }),

                Forms\Components\Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(function (Forms\Get $get) {
                        $guruId = $get('guru_id');
                        if ($guruId) {
                            $guru = Guru::with('mataPelajaran')->find($guruId);
                            if ($guru && $guru->mataPelajaran) {
                                return [$guru->mata_pelajaran_id => $guru->mataPelajaran->nama];
                            }
                        }
                        return MataPelajaran::pluck('nama', 'id');
                    })
                    ->required()
                    ->disabled(false) // jangan disable
                    ->reactive()      // wajib agar ter-update
                    ->dehydrated(),   // tetap dikirim ke server

                // Field Semester
                Forms\Components\Select::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),

                // Field Hari
                Forms\Components\Select::make('hari')
                    ->label('Hari')
                    ->options([
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                    ])
                    ->required(),
            ]);
    }
    public static function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isGuru = $user && $user->isGuru();
        $guru = $isGuru ? Guru::where('user_id', $user->id)->first() : null;

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->formatStateUsing(function ($state) {
                        $hari = [
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            7 => 'Minggu',
                        ];
                        return $hari[$state] ?? '-';
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->visible(!$isGuru) // Sembunyikan untuk guru
                    ->searchable()
                    ->preload(),

                // Filter khusus untuk guru (hanya menampilkan kelas yang dia pegang)
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->options(function () use ($guru) {
                        if ($guru && $guru->kelas_id) {
                            return [$guru->kelas_id => $guru->kelas->nama];
                        }
                        return [];
                    })
                    ->visible($isGuru && $guru && $guru->kelas_id) // Hanya tampilkan jika guru memiliki kelas
                    ->default($guru->kelas_id ?? null),

                Tables\Filters\SelectFilter::make('guru_id')
                    ->label('Guru')
                    ->options(function () use ($guru) {
                        if ($guru) {
                            return Guru::where('id', $guru->id)
                                ->pluck('nama_lengkap', 'id');
                        }
                        return Guru::query()->pluck('nama_lengkap', 'id');
                    })
                    ->default($guru->id ?? null),

                Tables\Filters\SelectFilter::make('hari')
                    ->label('Hari')
                    ->options([
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record): bool => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record): bool => static::canDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn(): bool => static::canDeleteAny()),
                ]),
            ]);
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
                return $query->where(function ($q) use ($guru) {
                    $q->where('guru_id', $guru->id)
                        ->orWhere('kelas_id', $guru->kelas_id);
                });
            }
            return $query->where('id', 0);
        }

        if ($user->isSiswa() && $user->kelas_id) {
            return $query->where('kelas_id', $user->kelas_id);
        }

        return $query;
    }

    // ... (sisanya tetap sama)


    public static function getRelations(): array
    {
        return [
            // Add relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalPelajarans::route('/'),
            'create' => Pages\CreateJadwalPelajaran::route('/create'),
            'edit' => Pages\EditJadwalPelajaran::route('/{record}/edit'),
        ];
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
        // Guru can only edit their own schedules
        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            return $guru && $record->guru_id === $guru->id;
        }

        return self::getCurrentUserRolePermissions('edit');
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        // Guru can only delete their own schedules
        /** @var \App\Models\User $user */
        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            return $guru && $record->guru_id === $guru->id;
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
                'viewAny' => false,
                'create' => false,
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
