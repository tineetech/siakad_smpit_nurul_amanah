<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StafResource\Pages;
use App\Filament\Resources\StafResource\RelationManagers;
use App\Models\Staf; // Import model Staf
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Validation\Rule;

class StafResource extends Resource
{
    protected static ?string $model = Staf::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group'; // Ikon grup pengguna
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
                    ->placeholder('Masukkan nama lengkap staf'),
                Forms\Components\Select::make('jabatan')
                    ->options([
                        'tata usaha' => 'Tata Usaha',
                        'kepala sekolah' => 'Kepala Sekolah',
                        'administrasi' => 'Administrasi',
                        'panitia ppdb' => 'Panitia PPDB',
                        // Tambahkan jabatan lain jika diperlukan
                    ])
                    ->required()
                    ->label('Jabatan')
                    ->placeholder('Pilih jabatan staf'),
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
                    ->placeholder('Masukkan tempat lahir staf'),
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
                    ->placeholder('Pilih agama staf'),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-aktif',
                    ])
                    ->required()
                    ->default('aktif')
                    ->label('Status Staf')
                    ->placeholder('Pilih status staf'),
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
                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable()
                    ->sortable()
                    ->label('Jabatan'),
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
                SelectFilter::make('jabatan')
                    ->options([
                        'tata usaha' => 'Tata Usaha',
                        'kepala sekolah' => 'Kepala Sekolah',
                        'administrasi' => 'Administrasi',
                        'panitia ppdb' => 'Panitia PPDB',
                    ])
                    ->placeholder('Semua Jabatan'),
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
            'index' => Pages\ListStafs::route('/'),
            'create' => Pages\CreateStaf::route('/create'),
            'edit' => Pages\EditStaf::route('/{record}/edit'),
        ];
    }
}