<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanSppResource\Pages;
use App\Models\PengaturanSpp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengaturanSppResource extends Resource
{
    protected static ?string $model = PengaturanSpp::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'POS SPP';
    protected static ?string $navigationLabel = 'Pengaturan SPP';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Pengaturan SPP')
                    ->required()
                    ->placeholder('Masukan nama SPP')
                    ->maxLength(255),
                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah SPP Default')
                    ->numeric()
                    ->prefix('Rp')
                    ->inputMode('decimal')
                    ->placeholder('Masukan jumlah SPP')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai Berlaku')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir Berlaku')
                    ->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pengaturan SPP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai Berlaku')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_berakhir')
                    ->label('Berakhir Berlaku')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPengaturanSpps::route('/'),
            'create' => Pages\CreatePengaturanSpp::route('/create'),
            'edit' => Pages\EditPengaturanSpp::route('/{record}/edit'),
        ];
    }
}