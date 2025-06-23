<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Models\Kelas; // Import model Kelas
use App\Models\Siswa; // Import model Siswa
use App\Models\Guru; // Import model Guru
use App\Models\Semester; // Import model Semester
use App\Models\Kurikulum; // Import model Kurikulum
use App\Models\Enrollment; // Import model Enrollment
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset; // Untuk mengelompokkan input enrollment
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

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
                    ->placeholder('Pilih guru sebagai wali kelas'),

                Fieldset::make('Enrollment Siswa')
                    ->schema([
                        Select::make('selected_semester_id')
                            ->label('Semester Terdaftar')
                            ->options(Semester::pluck('nama', 'id'))
                            ->required()
                            ->default(function () {
                                // Default ke semester aktif saat membuat record baru
                                return Semester::where('is_aktif', true)->first()?->id;
                            })
                            ->helperText('Pilih semester untuk enrollment siswa ini.')
                            ->columnSpanFull(),

                        Select::make('selected_kurikulum_id')
                            ->label('Kurikulum Terdaftar')
                            ->options(Kurikulum::pluck('nama', 'id'))
                            ->required()
                            ->default(function () {
                                // Default ke kurikulum aktif saat membuat record baru
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
                                // Untuk record baru, ini akan kosong
                                // Untuk record yang diedit, mutateFormDataBeforeFill akan mengisi ini
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guru_id')
                    ->label('Wali Kelas')
                    ->options(Guru::pluck('nama_lengkap', 'id'))
                    ->placeholder('Semua Wali Kelas'),
                Tables\Filters\SelectFilter::make('tingkat')
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
        return [
            //
        ];
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

        // Hapus enrollment siswa yang ada untuk kelas, semester, dan kurikulum yang dipilih
        $kelas->enrollments()
            ->where('semester_id', $selectedSemesterId)
            ->where('kurikulum_id', $selectedKurikulumId)
            ->whereNotNull('siswa_id') // Pastikan hanya enrollment siswa yang dihapus
            ->delete();

        // Tambahkan siswa yang dipilih
        foreach ($enrolledSiswaIds as $siswaId) {
            Enrollment::create([
                'kelas_id'      => $kelas->id,
                'siswa_id'      => $siswaId,
                'guru_id'       => null, // Pastikan ini null untuk enrollment siswa
                'semester_id'   => $selectedSemesterId,
                'kurikulum_id'  => $selectedKurikulumId,
                'nama'          => 'Enrollment Siswa', // Nama opsional
            ]);
        }

        Notification::make()
            ->title('Enrollment Berhasil Disinkronkan')
            ->body('Data siswa di kelas ini berhasil diperbarui.') // Sesuaikan pesan notifikasi
            ->success()
            ->send();
    }
}