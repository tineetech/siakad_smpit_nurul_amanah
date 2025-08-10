<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataPelajaranResource\Pages;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MataPelajaranResource extends Resource
{
    protected static ?string $model = MataPelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Nama Mata Pelajaran')
                    ->placeholder('Contoh: Matematika, Fiqih'),
                Forms\Components\TextInput::make('kode')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->nullable()
                    ->label('Kode Mata Pelajaran')
                    ->placeholder('Contoh: MTK001, FQH002'),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'umum' => 'Umum',
                        'kepesantrenan' => 'Kepesantrenan',
                        'ekstrakurikuler' => 'Ekstrakurikuler', // Contoh tambahan
                    ])
                    ->nullable()
                    ->label('Jenis Mata Pelajaran')
                    ->placeholder('Pilih jenis mata pelajaran'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Mata Pelajaran'),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable()
                    ->label('Kode'),
                Tables\Columns\TextColumn::make('gurus.nama_lengkap')
                    ->searchable()
                    ->default('-')
                    ->sortable()
                    ->label('Guru'),
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable()
                    ->sortable()
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'umum' => 'Umum',
                        'kepesantrenan' => 'Kepesantrenan',
                        'ekstrakurikuler' => 'Ekstrakurikuler',
                    ])
                    ->placeholder('Semua Jenis'),
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
            'index' => Pages\ListMataPelajarans::route('/'),
            'create' => Pages\CreateMataPelajaran::route('/create'),
            'edit' => Pages\EditMataPelajaran::route('/{record}/edit'),
        ];
    }

    // Role permission logic tetap kamu pakai:
    public static function canViewAny(): bool
    {
        return self::getCurrentUserRolePermissions('viewAny');
    }

    public static function canCreate(): bool
    {
        return self::getCurrentUserRolePermissions('create');
    }

    public static function canEdit(Model $record): bool
    {
        return self::getCurrentUserRolePermissions('edit');
    }

    public static function canDelete(Model $record): bool
    {
        return self::getCurrentUserRolePermissions('delete');
    }

    protected static function getCurrentUserRolePermissions(string $action): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $rolePermissions = [
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
            User::ROLE_KEPSEK => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
            User::ROLE_GURU => [
                'viewAny' => false,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }
}
