<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nip')
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->required() // Changed from nullable to required since we'll use it for email
                ->label('NIP'),
            Forms\Components\TextInput::make('nama_lengkap')
                ->required()
                ->maxLength(255)
                ->label('Nama Lengkap'),
            Forms\Components\Select::make('jenis_kelamin')
                ->options(['laki-laki' => 'Laki-laki', 'perempuan' => 'Perempuan'])
                ->required()
                ->label('Jenis Kelamin'),
            Forms\Components\TextInput::make('tempat_lahir')
                ->maxLength(255)
                ->nullable()
                ->label('Tempat Lahir'),
            Forms\Components\DatePicker::make('tanggal_lahir')
                ->nullable()
                ->label('Tanggal Lahir'),
            Forms\Components\Select::make('agama')
                ->options([
                    'Islam' => 'Islam',
                    'Kristen Protestan' => 'Kristen Protestan',
                    'Kristen Katolik' => 'Kristen Katolik',
                    'Hindu' => 'Hindu',
                    'Buddha' => 'Buddha',
                    'Konghucu' => 'Konghucu',
                ])
                ->nullable()
                ->label('Agama'),
            Forms\Components\Select::make('mata_pelajaran_id')
                ->label('Mata Pelajaran Diampu')
                ->options(MataPelajaran::pluck('nama', 'id'))
                ->nullable()
                ->searchable(),
            Forms\Components\Select::make('kelas_id')
                ->label('Wali Kelas (Opsional)')
                ->options(Kelas::pluck('nama', 'id'))
                ->nullable()
                ->searchable(),
            Forms\Components\Select::make('status')
                ->options(['aktif' => 'Aktif', 'non-aktif' => 'Non-aktif'])
                ->required()
                ->default('aktif')
                ->label('Status Guru'),
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
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Wali Kelas')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non-aktif' => 'warning',
                    })
                    ->sortable()
                    ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->options(['laki-laki' => 'Laki-laki', 'perempuan' => 'Perempuan']),
                SelectFilter::make('agama')
                    ->options([
                        'Islam' => 'Islam',
                        'Kristen Protestan' => 'Kristen Protestan',
                        'Kristen Katolik' => 'Kristen Katolik',
                        'Hindu' => 'Hindu',
                        'Buddha' => 'Buddha',
                        'Konghucu' => 'Konghucu',
                    ]),
                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(MataPelajaran::pluck('nama', 'id')),
                SelectFilter::make('status')
                    ->options(['aktif' => 'Aktif', 'non-aktif' => 'Non-aktif']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Guru $record, Tables\Actions\DeleteAction $action) {
                        // Delete associated user when guru is deleted
                        if ($record->user) {
                            $record->user->delete();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete associated users when gurus are deleted
                            $userIds = $records->pluck('user_id')->filter();
                            if ($userIds->count() > 0) {
                                User::whereIn('id', $userIds)->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }

    // ======================
    // === PRIVILEGE HERE ===
    // ======================

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
            User::ROLE_GURU => [
                'viewAny' => true,
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
            // Other roles have no access by default
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }

    // Handle user creation when guru is created or updated
    public static function createUserForGuru(Guru $guru): void
    {
        $namaDepan = explode(' ', $guru->nama_lengkap)[0];
        $namaLengkapBersih = strtolower($namaDepan);
        $email = $namaLengkapBersih . '@gmail.com';
        // $email = $guru->nip . '@gmail.com';
        // $password = "$guru->nip";
        $password = "guru123";

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $guru->nama_lengkap,
                'password' => Hash::make($password),
                'role' => User::ROLE_GURU,
            ]
        );

        // Update the guru record with the user_id
        $guru->user_id = $user->id;
        $guru->save();
    }
}