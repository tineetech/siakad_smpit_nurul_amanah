<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasSiswaResource\Pages;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;



class KelasSiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Kelas';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?int $navigationSort = -2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nisn')
                    ->required()
                    ->maxLength(10),

                Forms\Components\TextInput::make('nis')
                    ->required()
                    ->maxLength(8),

                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('tempat_lahir')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->required(),

                Forms\Components\Select::make('agama')
                    ->options([
                        'Islam' => 'Islam',
                        'Kristen' => 'Kristen',
                        'Katolik' => 'Katolik',
                        'Hindu' => 'Hindu',
                        'Buddha' => 'Buddha',
                        'Konghucu' => 'Konghucu',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('nama_ayah')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama_ibu')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nomor_telepon_orang_tua')
                    ->tel()
                    ->maxLength(15),

                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-Aktif',
                        'alumni' => 'Alumni',
                    ])
                    ->required(),

                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'nama')
                    ->required()
                    ->hidden(fn() => Auth::user()->role !== User::ROLE_ADMIN),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->searchable(),


                Tables\Columns\TextColumn::make('nis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan'),

                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non-aktif' => 'danger',
                        'alumni' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas')
                    ->relationship('kelas', 'nama')
                    ->hidden(fn() => Auth::user()->role === User::ROLE_SISWA),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-Aktif',
                        'alumni' => 'Alumni',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1=0');
        }

        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_TATA_USAHA])) {
            return $query;
        }

        if ($user->role === User::ROLE_GURU) {
            // Cari data guru berdasarkan user_id
            $guru = Guru::where('user_id', $user->id)->first();

            if (!$guru) {
                return $query->whereRaw('1=0');
            }

            // Jika guru adalah wali kelas (memiliki kelas_id)
            if ($guru->kelas_id) {
                return $query->where('kelas_id', $guru->kelas_id);
            }

            return $query->whereRaw('1=0');
        }

        if ($user->role === User::ROLE_SISWA) {
            $siswa = Siswa::where('user_id', $user->id)->first();

            if ($siswa) {
                return $query->where('kelas_id', $siswa->kelas_id);
            }
            return $query->whereRaw('1=0');
        }

        return $query->whereRaw('1=0');
    }
    public static function getRelations(): array
    {
        return [
            // Add relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelasSiswas::route('/'),
            'create' => Pages\CreateKelasSiswa::route('/create'),
            'edit' => Pages\EditKelasSiswa::route('/{record}/edit'),
        ];
    }
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
                'viewAny' => false,
                'create' => false,
                'edit' => false,
                'delete' => false,
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
            User::ROLE_SISWA => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
        ];

        return $permissions[$user->role][$action] ?? false;
    }
}
