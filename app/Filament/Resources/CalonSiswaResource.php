<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalonSiswaResource\Pages;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class CalonSiswaResource extends Resource
{
    protected static ?string $model = CalonSiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Calon Siswa';
    protected static ?string $navigationGroup = 'PPDB';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('gelombang_id')
                    ->label('Gelombang')
                    ->relationship('gelombang', 'nama')
                    ->required(),

                Forms\Components\TextInput::make('nomor_pendaftaran')
                    ->label('Nomor Pendaftaran')
                    ->required()
                    ->unique(),

                Forms\Components\TextInput::make('nisn')->label('NISN'),
                Forms\Components\TextInput::make('nis')->label('NIS'),

                Forms\Components\TextInput::make('nama_lengkap')->label('Nama Lengkap')->required(),

                Forms\Components\FileUpload::make('profile_picture')
                    ->label('Foto Profil')
                    ->directory('images/pp')
                    ->image()
                    ->nullable(),

                Forms\Components\FileUpload::make('surat_kelulusan')
                    ->label('Surat Kelulusan')
                    ->directory('berkas/surat_kelulusan')
                    ->nullable(),

                Forms\Components\FileUpload::make('akta_kelahiran')
                    ->label('Akta Kelahiran')
                    ->directory('berkas/akta_kelahiran')
                    ->nullable(),

                Forms\Components\FileUpload::make('kartu_keluarga')
                    ->label('Kartu Keluarga')
                    ->directory('berkas/kartu_keluarga')
                    ->nullable(),

                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->nullable()
                    ->label('Jenis Kelamin'),

                Forms\Components\TextInput::make('tempat_lahir')->label('Tempat Lahir'),
                Forms\Components\DatePicker::make('tanggal_lahir')->label('Tanggal Lahir'),
                Forms\Components\Textarea::make('alamat')->label('Alamat'),
                Forms\Components\TextInput::make('nama_orang_tua')->label('Nama Orang Tua'),
                Forms\Components\TextInput::make('nomor_telepon_orang_tua')->label('Nomor Telepon Orang Tua'),
                Forms\Components\TextInput::make('email')->label('Email'),

                Forms\Components\DateTimePicker::make('tanggal_pendaftaran')
                    ->label('Tanggal Pendaftaran')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('menunggu')
                    ->label('Status'),

                Forms\Components\Select::make('disetujui_oleh_user_id')
                    ->label('Disetujui Oleh')
                    ->relationship('disetujuiOleh', 'name')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('tanggal_persetujuan')->label('Tanggal Persetujuan')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pendaftaran')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_lengkap')->searchable(),
                Tables\Columns\TextColumn::make('gelombang.nama')->label('Gelombang'),
                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'secondary' => 'menunggu',
                    'success' => 'disetujui',
                    'danger' => 'ditolak',
                ]),
                Tables\Columns\TextColumn::make('tanggal_pendaftaran')->dateTime('d M Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCalonSiswas::route('/'),
            'create' => Pages\CreateCalonSiswa::route('/create'),
            'edit' => Pages\EditCalonSiswa::route('/{record}/edit'),
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
