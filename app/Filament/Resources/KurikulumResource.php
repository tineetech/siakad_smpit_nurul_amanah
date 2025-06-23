<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KurikulumResource\Pages;
use App\Models\Kurikulum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class KurikulumResource extends Resource
{
    protected static ?string $model = Kurikulum::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Nama Kurikulum')
                    ->placeholder('Contoh: Kurikulum Merdeka'),
                Textarea::make('deskripsi')
                    ->nullable()
                    ->rows(3)
                    ->label('Deskripsi')
                    ->placeholder('Masukkan deskripsi singkat tentang kurikulum ini.'),
                TextInput::make('tahun_mulai')
                    ->numeric()
                    ->nullable()
                    ->minLength(4)
                    ->maxLength(4)
                    ->label('Tahun Mulai')
                    ->placeholder('Contoh: 2022'),
                TextInput::make('tahun_berakhir')
                    ->numeric()
                    ->nullable()
                    ->minLength(4)
                    ->maxLength(4)
                    ->label('Tahun Berakhir')
                    ->placeholder('Contoh: 2025'),
                Toggle::make('is_aktif')
                    ->label('Aktifkan Kurikulum Ini?')
                    ->helperText('Hanya satu kurikulum yang bisa aktif pada satu waktu.')
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
                    ->label('Nama Kurikulum'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Deskripsi'),
                Tables\Columns\TextColumn::make('tahun_mulai')
                    ->sortable()
                    ->label('Tahun Mulai'),
                Tables\Columns\TextColumn::make('tahun_berakhir')
                    ->sortable()
                    ->label('Tahun Berakhir'),
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
            'index' => Pages\ListKurikulums::route('/'),
            'create' => Pages\CreateKurikulum::route('/create'),
            'edit' => Pages\EditKurikulum::route('/{record}/edit'),
        ];
    }
}