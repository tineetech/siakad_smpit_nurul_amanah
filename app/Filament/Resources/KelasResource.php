<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\Kurikulum;
use App\Models\Enrollment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 7;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: X IPA 1, XI IPS 2'),
                Forms\Components\TextInput::make('tingkat')
                    ->maxLength(255)
                    ->nullable()
                    ->label('Tingkat')
                    ->placeholder('Contoh: X, XI, XII'),
                Forms\Components\TextInput::make('kapasitas')
                    ->numeric()
                    ->default(35)
                    ->nullable()
                    ->label('Kapasitas Siswa')
                    ->placeholder('Contoh: 35'),
                Select::make('guru_id')
                    ->label('Wali Kelas')
                    ->options(Guru::pluck('nama_lengkap', 'id'))
                    ->nullable()
                    ->searchable()
                    ->placeholder('Pilih guru sebagai wali kelas')
                    // Display current wali kelas name when editing
                    ->hint(fn($record) => $record?->guru ? 'Current: ' . $record->guru->nama_lengkap : ''),

                Fieldset::make('Enrollment Siswa')
                    ->schema([
                        Select::make('selected_semester_id')
                            ->label('Semester Terdaftar')
                            ->options(Semester::pluck('nama', 'id'))
                            ->required()
                            ->default(function () {
                                return Semester::where('is_aktif', true)->first()?->id;
                            })
                            ->helperText('Pilih semester untuk enrollment siswa ini.')
                            ->columnSpanFull(),

                        Select::make('selected_kurikulum_id')
                            ->label('Kurikulum Terdaftar')
                            ->options(Kurikulum::pluck('nama', 'id'))
                            ->required()
                            ->default(function () {
                                return Kurikulum::where('is_aktif', true)->first()?->id;
                            })
                            ->helperText('Pilih kurikulum untuk enrollment siswa ini.')
                            ->columnSpanFull(),

                        Select::make('enrolled_siswa')
                            ->label('Siswa Terdaftar')
                            ->options(Siswa::pluck('nama_lengkap', 'id'))
                            ->multiple()
                            ->searchable()
                            ->placeholder('Pilih siswa yang akan didaftarkan')
                            ->helperText('Pilih siswa yang akan dimasukkan ke kelas ini untuk semester dan kurikulum terpilih.')
                            ->default(function (Forms\Get $get) {
                                if (!$get('selected_semester_id') || !$get('selected_kurikulum_id')) return [];
                                return [];
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Kelas'),
                Tables\Columns\TextColumn::make('tingkat')
                    ->searchable()
                    ->sortable()
                    ->label('Tingkat'),
                Tables\Columns\TextColumn::make('kapasitas')
                    ->numeric()
                    ->sortable()
                    ->label('Kapasitas'),
                Tables\Columns\TextColumn::make('waliKelas.nama_lengkap')
                    ->label('Wali Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
            ])
            ->filters([
                SelectFilter::make('guru_id')
                    ->label('Wali Kelas')
                    ->options(Guru::pluck('nama_lengkap', 'id'))
                    ->placeholder('Semua Wali Kelas'),
                SelectFilter::make('tingkat')
                    ->options(Kelas::distinct()->pluck('tingkat', 'tingkat')->filter()->toArray())
                    ->placeholder('Semua Tingkat'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }

    public static function syncEnrollments(array $data, Kelas $kelas): void
    {
        $selectedSemesterId = $data['selected_semester_id'] ?? null;
        $selectedKurikulumId = $data['selected_kurikulum_id'] ?? null;
        $enrolledSiswaIds = $data['enrolled_siswa'] ?? [];

        if (!$selectedSemesterId || !$selectedKurikulumId) {
            Notification::make()
                ->title('Peringatan Enrollment')
                ->body('Semester dan Kurikulum harus dipilih untuk mengelola enrollment.')
                ->warning()
                ->send();
            return;
        }

        $kelas->enrollments()
            ->where('semester_id', $selectedSemesterId)
            ->where('kurikulum_id', $selectedKurikulumId)
            ->whereNotNull('siswa_id')
            ->delete();

        foreach ($enrolledSiswaIds as $siswaId) {
            Enrollment::create([
                'kelas_id'      => $kelas->id,
                'siswa_id'      => $siswaId,
                'guru_id'       => null,
                'semester_id'   => $selectedSemesterId,
                'kurikulum_id'  => $selectedKurikulumId,
                'nama'          => 'Enrollment Siswa',
            ]);
        }

        Notification::make()
            ->title('Enrollment Berhasil Disinkronkan')
            ->body('Data siswa di kelas ini berhasil diperbarui.')
            ->success()
            ->send();
    }

    // ======================
    // === PRIVILEGE HERE ===
    // ======================

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
        return self::getCurrentUserRolePermissions('edit');
    }

    public static function canDelete(Model $record): bool
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
                'viewAny' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
            User::ROLE_GURU => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }
}
