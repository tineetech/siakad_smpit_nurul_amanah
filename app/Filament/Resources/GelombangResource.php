<?php
namespace App\Filament\Resources;

use App\Filament\Resources\GelombangResource\Pages;
use App\Models\Gelombang;
use App\Exports\GelombangExport;
use App\Imports\GelombangImport;
use App\Models\User;
use Filament\{Tables, Forms, Resources\Resource};
use Filament\Forms\Components\{TextInput, DatePicker, FileUpload, Select};
use Filament\Tables\Actions;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class GelombangResource extends Resource
{
    protected static ?string $model = Gelombang::class;
    protected static ?string $navigationGroup = 'Portal PPDB';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('nama')->label('Nama Gelombang')->required()->placeholder('Contoh: Gelombang 1'),
            TextInput::make('kouta')->label('Kuota')->numeric()->required()->placeholder('Jumlah peserta maksimal'),
            TextInput::make('kouta_terisi')->label('Kuota Terisi')->default(0)->numeric()->required()->placeholder('Minimal 0/1'),
            DatePicker::make('tanggal_mulai')->label('Tanggal Mulai')->required()->placeholder('Pilih tanggal mulai'),
            DatePicker::make('tanggal_berakhir')->label('Tanggal Berakhir')->required()->placeholder('Pilih tanggal akhir'),
            Select::make('created_by')->label('Dibuat Oleh')->options(User::pluck('name', 'id'))->required()->placeholder('Nama user pembuat'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('kouta'),
                Tables\Columns\TextColumn::make('kouta_terisi'),
                Tables\Columns\TextColumn::make('tanggal_mulai')->date(),
                Tables\Columns\TextColumn::make('tanggal_berakhir')->date(),
            ])
            ->filters([])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGelombangs::route('/'),
            'create' => Pages\CreateGelombang::route('/create'),
            'edit' => Pages\EditGelombang::route('/{record}/edit'),
        ];
    }
}
