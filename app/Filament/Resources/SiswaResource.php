<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Siswa')
                    ->placeholder('Masukkan nama lengkap siswa'),
                Forms\Components\TextInput::make('nisn')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('NISN')
                    ->placeholder('Masukkan NISN siswa (misal: 1234567890)'),
                Forms\Components\TextInput::make('nis')
                    ->nullable()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('NIS (Nomor Induk Siswa)')
                    ->placeholder('Masukkan NIS siswa (opsional)'),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->required()
                    ->label('Jenis Kelamin')
                    ->placeholder('Pilih jenis kelamin'),
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
                    ->placeholder('Pilih agama siswa'),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(100)
                    ->nullable()
                    ->label('Tempat Lahir')
                    ->placeholder('Masukkan tempat lahir siswa'),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->nullable()
                    ->label('Tanggal Lahir')
                    ->placeholder('Pilih tanggal lahir'),
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(Kelas::pluck('nama', 'id'))
                    ->nullable()
                    ->searchable()
                    ->placeholder('Pilih kelas siswa'),
                Forms\Components\TextInput::make('nomor_telepon_orang_tua')
                    ->nullable()
                    ->maxLength(20)
                    ->tel()
                    ->label('No. HP Orang Tua')
                    ->placeholder('Masukkan nomor HP orang tua'),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-aktif',
                        'lulus' => 'Lulus',
                    ])
                    ->required()
                    ->default('aktif')
                    ->label('Status Siswa')
                    ->placeholder('Pilih status siswa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Siswa'),
                Tables\Columns\TextColumn::make('nisn')
                    ->searchable()
                    ->sortable()
                    ->label('NISN'),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->sortable(),
                Tables\Columns\TextColumn::make('agama')
                    ->label('Agama')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_telepon_orang_tua')
                    ->label('No. HP Ortu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non-aktif' => 'warning',
                        'lulus' => 'info',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tanggal Ditambahkan'),
            ])
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Filter Berdasarkan Kelas')
                    ->options(Kelas::pluck('nama', 'id'))
                    ->placeholder('Semua Kelas'),
                SelectFilter::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->placeholder('Semua Jenis Kelamin'),
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
                        'lulus' => 'Lulus',
                    ])
                    ->placeholder('Semua Status Siswa'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Siswa $record, Tables\Actions\DeleteAction $action) {
                        // Delete associated user when siswa is deleted
                        if ($record->user) {
                            $record->user->delete();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete associated users when students are deleted
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }

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
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }

    // Handle user creation when siswa is created or updated
    public static function createUserForSiswa(Siswa $siswa): void
    {
        $namaDepan = explode(' ', $siswa->nama_lengkap)[0];
        $namaLengkapBersih = strtolower($namaDepan);
        $email = $namaLengkapBersih . '@gmail.com';
        // $password = $siswa->nisn;
        $password = "siswa123";

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $siswa->nama_lengkap,
                'password' => Hash::make($password),
                'role' => User::ROLE_SISWA,
            ]
        );

        // Update the siswa record with the user_id
        $siswa->user_id = $user->id;
        $siswa->save();
    }
}