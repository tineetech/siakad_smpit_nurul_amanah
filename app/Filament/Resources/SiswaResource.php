<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa; // Pastikan model Siswa sudah diimpor
use App\Models\Kelas; // Import model Kelas
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select; // Untuk dropdown
use Filament\Tables\Filters\SelectFilter; // Untuk filter dropdown

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Siswa')
                    ->placeholder('Masukkan nama lengkap siswa'), // Placeholder
                Forms\Components\TextInput::make('nisn')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('NISN')
                    ->placeholder('Masukkan NISN siswa (misal: 1234567890)'), // Placeholder
                Forms\Components\TextInput::make('nis')
                    ->nullable()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('NIS (Nomor Induk Siswa)')
                    ->placeholder('Masukkan NIS siswa (opsional)'), // Placeholder
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->required()
                    ->label('Jenis Kelamin')
                    ->placeholder('Pilih jenis kelamin'), // Placeholder
                
                // REVISI: Agama menjadi Select dengan pilihan 6 agama
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
                    ->placeholder('Pilih agama siswa'), // Placeholder
                
                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(100)
                    ->nullable()
                    ->label('Tempat Lahir')
                    ->placeholder('Masukkan tempat lahir siswa'), // Placeholder
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->nullable()
                    ->label('Tanggal Lahir')
                    ->placeholder('Pilih tanggal lahir'), // Placeholder
                
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(Kelas::pluck('nama', 'id'))
                    ->nullable()
                    ->searchable()
                    ->placeholder('Pilih kelas siswa'), // Placeholder
                Forms\Components\TextInput::make('nomor_telepon_orang_tua')
                    ->nullable()
                    ->maxLength(20)
                    ->tel()
                    ->label('No. HP Orang Tua')
                    ->placeholder('Masukkan nomor HP orang tua'), // Placeholder
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-aktif',
                        'lulus' => 'Lulus',
                    ])
                    ->required()
                    ->default('aktif')
                    ->label('Status Siswa')
                    ->placeholder('Pilih status siswa'), // Placeholder
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Siswa'),
                Tables\Columns\TextColumn::make('nisn')
                    ->searchable()
                    ->sortable()
                    ->label('NISN'),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->sortable(),
                Tables\Columns\TextColumn::make('agama')
                    ->label('Agama')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_telepon_orang_tua')
                    ->label('No. HP Ortu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non-aktif' => 'warning',
                        'lulus' => 'info',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tanggal Ditambahkan'),
            ])
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Filter Berdasarkan Kelas')
                    ->options(Kelas::pluck('nama', 'id'))
                    ->placeholder('Semua Kelas'),
                SelectFilter::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->placeholder('Semua Jenis Kelamin'),
                // REVISI: Filter Agama
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
                SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-aktif',
                        'lulus' => 'Lulus',
                    ])
                    ->placeholder('Semua Status Siswa'),
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}