<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KurikulumResource\Pages;
use App\Models\Kurikulum;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
                    ->label('Nama Kurikulum'),
                Forms\Components\Textarea::make('deskripsi')
                    ->nullable()
                    ->rows(3)
                    ->label('Deskripsi'),
                Forms\Components\TextInput::make('tahun_mulai')
                    ->numeric()
                    ->nullable()
                    ->minLength(4)
                    ->maxLength(4)
                    ->label('Tahun Mulai'),
                Forms\Components\TextInput::make('tahun_berakhir')
                    ->numeric()
                    ->nullable()
                    ->minLength(4)
                    ->maxLength(4)
                    ->label('Tahun Berakhir'),
                Forms\Components\Toggle::make('is_aktif')
                    ->label('Aktifkan Kurikulum Ini?')
                    ->helperText('Hanya satu kurikulum yang bisa aktif pada satu waktu.')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->searchable()->sortable()->label('Nama Kurikulum'),
                Tables\Columns\TextColumn::make('deskripsi')->limit(50)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tahun_mulai')->sortable(),
                Tables\Columns\TextColumn::make('tahun_berakhir')->sortable(),
                Tables\Columns\IconColumn::make('is_aktif')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_aktif')->label('Status Aktif')->nullable()
                    ->trueLabel('Aktif')->falseLabel('Tidak Aktif')->placeholder('Semua'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKurikulums::route('/'),
            'create' => Pages\CreateKurikulum::route('/create'),
            'edit' => Pages\EditKurikulum::route('/{record}/edit'),
        ];
    }

    // CUSTOM PERMISSIONS

    public static function canViewAny(): bool
    {
        return self::checkPermission('viewAny');
    }

    public static function canCreate(): bool
    {
        return self::checkPermission('create');
    }

    public static function canEdit(Model $record): bool
    {
        return self::checkPermission('edit');
    }

    public static function canDelete(Model $record): bool
    {
        return self::checkPermission('delete');
    }

    protected static function checkPermission(string $action): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $permissions = [
            User::ROLE_ADMIN => [
                'viewAny' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
            User::ROLE_TATA_USAHA => [
                'viewAny' => false,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
            User::ROLE_GURU => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
        ];

        return $permissions[$user->role][$action] ?? false;
    }
}
