<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiGuruResource\Pages;
use App\Models\AbsensiGuru;
use App\Models\Guru;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AbsensiGuruResource extends Resource
{
    protected static ?string $model = AbsensiGuru::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationLabel = 'Absensi Guru';
    protected static ?int $navigationSort = 2;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('guru_id')
                    ->relationship('guru', 'nama_lengkap')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_absensi')->required(),
                Forms\Components\TimePicker::make('waktu_absensi')->required(),

                Forms\Components\Select::make('status_kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ])->required(),
                Forms\Components\Select::make('mode_absensi')
                    ->options([
                        'scan_qr' => 'Scan QR',
                        'manual' => 'Manual',
                    ])->required(),
                Forms\Components\Textarea::make('catatan'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guru.nama_lengkap')->label('Guru'),
                Tables\Columns\TextColumn::make('tanggal_absensi')->date(),
                Tables\Columns\TextColumn::make('waktu_absensi'),
                Tables\Columns\TextColumn::make('status_kehadiran')->label('Status'),
                Tables\Columns\TextColumn::make('mode_absensi')->label('Mode'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->hidden(fn() => !in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_TATA_USAHA])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiGurus::route('/'),
            'create' => Pages\CreateAbsensiGuru::route('/create'),
            'edit' => Pages\EditAbsensiGuru::route('/{record}/edit'),
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
                'viewAny' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
            User::ROLE_GURU => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }
}
