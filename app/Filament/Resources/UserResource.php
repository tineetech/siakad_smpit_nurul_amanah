<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $pluralModelLabel = 'Pengguna';
    protected static ?string $navigationLabel = 'Akun Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true), // Pastikan nama unik, abaikan saat mengedit
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true) // Pastikan email unik, abaikan saat mengedit
                    ->nullable(), // Sesuai dengan struktur tabel (bisa null)
                Forms\Components\TextInput::make('phone_number')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->maxLength(20)
                    ->nullable(),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->nullable(),
                Forms\Components\Select::make('role')
                    ->label('Peran')
                    ->options([
                        'admin' => 'Administrator',
                        'siswa' => 'Siswa',
                        'guru' => 'Guru',
                        'tata_usaha' => 'Tata Usaha',
                        'staff_ppdb' => 'Staff PPDB',
                        'staff' => 'Staff',
                        'kepala_sekolah' => 'Kepala Sekolah',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->revealable()
                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                Forms\Components\TextInput::make('password_confirmation') // Field konfirmasi password
                    ->label('Konfirmasi Password')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->visibleOn('create'), // Hanya terlihat saat membuat, karena password di-dehydrate di atas
                FileUpload::make('profile_picture')
                    ->label('Foto Profil')
                    ->directory('images/pp') // Direktori penyimpanan di storage/app/public
                    ->image() // Validasi bahwa ini adalah file gambar
                    ->nullable()
                    ->default('images/pp/default.jpg'), // Menetapkan nilai default
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->label('Foto Profil')
                    ->circular()
                    ->defaultImageUrl(url('images/pp/default.jpg')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))), // Mengubah format tampilan role
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Nomor Telepon')
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Berdasarkan Peran')
                    ->options([
                        'admin' => 'Administrator',
                        'siswa' => 'Siswa',
                        'guru' => 'Guru',
                        'tata_usaha' => 'Tata Usaha',
                        'staff_ppdb' => 'Panitia PPDB',
                        'staff' => 'Staff',
                        'kepala_sekolah' => 'Kepala Sekolah',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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