<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Filament\Resources\GuruResource\RelationManagers;
use App\Models\Guru; // Import model Guru
use App\Models\MataPelajaran; // Import model MataPelajaran
use App\Models\Kelas; // Import model Kelas
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap'; // Ikon topi akademik
    protected static ?string $navigationGroup = 'Data Master'; // Kelompokkan di sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nip')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->nullable()
                    ->label('NIP')
                    ->placeholder('Masukkan Nomor Induk Pegawai'),
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap')
                    ->placeholder('Masukkan nama lengkap guru'),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->required()
                    ->label('Jenis Kelamin')
                    ->placeholder('Pilih jenis kelamin'),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(255)
                    ->nullable()
                    ->label('Tempat Lahir')
                    ->placeholder('Masukkan tempat lahir guru'),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->nullable()
                    ->label('Tanggal Lahir')
                    ->placeholder('Pilih tanggal lahir'),
                Forms\Components\Select::make('agama')
                    ->options([
                        'Islam'         => 'Islam',
                        'Kristen Protestan' => 'Kristen Protestan',
                        'Kristen Katolik' => 'Kristen Katolik',
                        'Hindu'         => 'Hindu',
                        'Buddha'        => 'Buddha',
                        'Konghucu'      => 'Konghucu',
                    ])
                    ->nullable()
                    ->label('Agama')
                    ->placeholder('Pilih agama guru'),
                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran Diampu')
                    ->options(MataPelajaran::pluck('nama', 'id'))
                    ->nullable()
                    ->searchable()
                    ->placeholder('Pilih mata pelajaran yang diampu'),
                Select::make('kelas_id')
                    ->label('Wali Kelas (Opsional)')
                    ->options(Kelas::pluck('nama', 'id'))
                    ->nullable()
                    ->searchable()
                    ->placeholder('Pilih kelas jika guru adalah wali kelas'),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-aktif',
                    ])
                    ->required()
                    ->default('aktif')
                    ->label('Status Guru')
                    ->placeholder('Pilih status guru'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->searchable()
                    ->sortable()
                    ->label('NIP'),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Lengkap'),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tempat Lahir'),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tanggal Lahir'),
                Tables\Columns\TextColumn::make('agama')
                    ->label('Agama')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mataPelajaran.nama') // Menampilkan nama mata pelajaran
                    ->label('Mata Pelajaran')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama') // Menampilkan nama kelas (wali kelas)
                    ->label('Wali Kelas')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non-aktif' => 'warning',
                    })
                    ->sortable()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->placeholder('Semua Jenis Kelamin'),
                SelectFilter::make('agama')
                    ->options([
                        'Islam'         => 'Islam',
                        'Kristen Protestan' => 'Kristen Protestan',
                        'Kristen Katolik' => 'Kristen Katolik',
                        'Hindu'         => 'Hindu',
                        'Buddha'        => 'Buddha',
                        'Konghucu'      => 'Konghucu',
                    ])
                    ->placeholder('Semua Agama'),
                SelectFilter::make('mata_pelajaran_id')
                    ->label('Filter Mata Pelajaran')
                    ->options(MataPelajaran::pluck('nama', 'id'))
                    ->placeholder('Semua Mata Pelajaran'),
                SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-aktif',
                    ])
                    ->placeholder('Semua Status'),
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
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }
}