<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalonSiswaResource\Pages;

use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Exports\CalonSiswaExport;
use App\Imports\CalonSiswaImport;
use Carbon\Carbon;
use Filament\{Tables, Forms, Resources\Resource};
use Filament\Forms\Components\{Select, TextInput, DatePicker, DateTimePicker, FileUpload};
use Filament\Tables\Actions;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CalonSiswaResource extends Resource
{
    protected static ?string $model = CalonSiswa::class;
    protected static ?string $navigationGroup = 'Portal PPDB';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('gelombang_id')->label('Gelombang')->options(Gelombang::pluck('nama','id'))->required()->default(function () {
                return Gelombang::whereColumn('kouta_terisi', '<', 'kouta')
                    ->whereDate('tanggal_mulai', '<=', Carbon::today())
                    ->orderByDesc('tanggal_mulai')
                    ->value('id'); // ambil id gelombang pertama yg sesuai
            })->placeholder('Pilih gelombang'),
            TextInput::make('nomor_pendaftaran')->label('Nomor Pendaftaran')->required()->placeholder('Nomor pendaftaran unik'),
            TextInput::make('nisn')->label('NISN')->nullable()->placeholder('Isi jika ada'),
            TextInput::make('nis')->label('NIS')->nullable()->placeholder('Isi jika ada'),
            TextInput::make('nama_lengkap')->label('Nama Lengkap')->required()->placeholder('Nama lengkap'),
            Select::make('jenis_kelamin')->label('Jenis Kelamin')
                ->options(['laki-laki'=>'Laki-laki','perempuan'=>'Perempuan'])
                ->required()->placeholder('-- Pilih jenis kelamin --'),
            TextInput::make('tempat_lahir')->label('Tempat Lahir')->nullable()->placeholder('Kota/Kabupaten'),
            DatePicker::make('tanggal_lahir')->label('Tanggal Lahir')->nullable()->placeholder('Pilih tanggal'),
            TextInput::make('alamat')->label('Alamat')->nullable()->placeholder('Alamat lengkap'),
            TextInput::make('nama_orang_tua')->label('Orang Tua')->nullable()->placeholder('Nama ayah/ibu'),
            TextInput::make('nomor_telepon_orang_tua')->label('Telepon Ortu')->nullable()->placeholder('0812xxxx'),
            TextInput::make('email')->label('Email')->nullable()->placeholder('Contoh: email@domain.com'),
            DateTimePicker::make('tanggal_pendaftaran')->label('Tanggal Daftar')->required()->default(now())->placeholder('Waktu pendaftaran'),
            Select::make('status')->label('Status')->options(['menunggu'=>'Menunggu','disetujui'=>'Disetujui','ditolak'=>'Ditolak'])->default('menunggu')->required()->placeholder('Pilih status'),
            Select::make('disetujui_oleh_user_id')->label('Disetujui Oleh')->options(\App\Models\User::pluck('name','id'))->nullable()->placeholder('Pilih petugas')->visible(fn (Page $livewire) => $livewire instanceof \App\Filament\Resources\PenetapanSppsResource\Pages\EditPenetapanSpps),
            DateTimePicker::make('tanggal_persetujuan')->label('Tanggal Persetujuan')->nullable()->placeholder('Waktu persetujuan')->visible(fn (Page $livewire) => $livewire instanceof \App\Filament\Resources\PenetapanSppsResource\Pages\EditPenetapanSpps),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gelombang.nama')->label('Gelombang'),
                Tables\Columns\TextColumn::make('nomor_pendaftaran'),
                Tables\Columns\TextColumn::make('nama_lengkap'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('tanggal_pendaftaran')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
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
            'index' => Pages\ListCalonSiswas::route('/'),
            'create' => Pages\CreateCalonSiswa::route('/create'),
            'edit' => Pages\EditCalonSiswa::route('/{record}/edit'),
        ];
    }
}
