<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SemesterResource\Pages;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;

class SemesterResource extends Resource
{
    protected static ?string $model = Semester::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Nama Semester')
                    ->placeholder('Contoh: Ganjil 2024/2025'),
                DatePicker::make('tanggal_mulai')
                    ->nullable()
                    ->label('Tanggal Mulai')
                    ->placeholder('Pilih tanggal mulai semester'),
                DatePicker::make('tanggal_berakhir')
                    ->nullable()
                    ->label('Tanggal Berakhir')
                    ->placeholder('Pilih tanggal berakhir semester'),
                Toggle::make('is_aktif')
                    ->label('Aktifkan Semester Ini?')
                    ->helperText('Hanya satu semester yang bisa aktif pada satu waktu.')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Semester'),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable()
                    ->label('Mulai'),
                Tables\Columns\TextColumn::make('tanggal_berakhir')
                    ->date()
                    ->sortable()
                    ->label('Berakhir'),
                Tables\Columns\IconColumn::make('is_aktif')
                    ->boolean()
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_aktif')
                    ->label('Status Aktif')
                    ->nullable()
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
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
            'index' => Pages\ListSemesters::route('/'),
            'create' => Pages\CreateSemester::route('/create'),
            'edit' => Pages\EditSemester::route('/{record}/edit'),
        ];
    }
}