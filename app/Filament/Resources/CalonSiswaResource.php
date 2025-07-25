<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalonSiswaResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use Carbon\Carbon;
use Filament\{Tables, Forms, Resources\Resource};
use Filament\Forms\Components\{Select, TextInput, DatePicker, DateTimePicker, FileUpload, Textarea, Fieldset, Section}; // Tambahkan Textarea, Fieldset, Section
use Filament\Tables\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CalonSiswaResource extends Resource
{
    protected static ?string $model = CalonSiswa::class;

    protected static ?string $navigationGroup = 'Portal SPMB';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Informasi Pendaftaran')
                ->description('Data dasar pendaftaran siswa dan gelombang.')
                ->schema([
                    Select::make('gelombang_id')
                        ->label('Gelombang')
                        ->options(Gelombang::pluck('nama', 'id'))
                        ->required()
                        ->default(function () {
                            // Ambil gelombang aktif yang kuotanya belum penuh dan tanggal mulai <= hari ini
                            return Gelombang::whereColumn('kouta_terisi', '<', 'kouta')
                                ->whereDate('tanggal_mulai', '<=', Carbon::today())
                                ->orderByDesc('tanggal_mulai')
                                ->value('id'); // ambil id gelombang pertama yang sesuai
                        })
                        ->placeholder('Pilih gelombang'),
                    TextInput::make('nomor_pendaftaran')
                        ->label('Nomor Pendaftaran')
                        ->required()
                        ->unique(ignoreRecord: true) // Pastikan nomor pendaftaran unik
                        ->maxLength(50)
                        ->placeholder('Nomor pendaftaran unik'),
                    DateTimePicker::make('tanggal_pendaftaran')
                        ->label('Tanggal Daftar')
                        ->required()
                        ->default(now())
                        ->native(false) // Gunakan Filament's custom date picker
                        ->displayFormat('d/m/Y H:i')
                        ->placeholder('Waktu pendaftaran'),
                ])->columns(3), // Mengatur layout kolom dalam Section

            Section::make('Data Pribadi Siswa')
                ->description('Data Calon siswa.')
                ->schema([
                    TextInput::make('nik')
                        ->label('NIK')
                        ->nullable()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->numeric() // Pastikan hanya angka
                        ->placeholder('Isi NIK siswa')
                        ->minLength(16)
                        ->maxLength(16),
                        TextInput::make('nisn')
                        ->label('NISN')
                        ->minLength(10)
                        ->maxLength(10)
                        ->nullable()
                        ->numeric() // Pastikan hanya angka
                        ->maxLength(255)
                        ->placeholder('Isi jika ada'),
                    TextInput::make('nis')
                        ->label('NIS')
                        ->nullable()
                        ->numeric() // Pastikan hanya angka
                        ->maxLength(255)
                        ->placeholder('Isi jika ada'),
                    TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Nama lengkap calon siswa'),
                    FileUpload::make('profile_picture')
                        ->label('Foto Profil')
                        ->image()
                        ->avatar() // Menampilkan sebagai avatar jika diinginkan
                        ->disk('public')
                        ->directory('profile-pictures/calon-siswa') // Direktori khusus untuk siswa
                        ->visibility('public')
                        ->nullable()
                        ->maxSize(2048), // Max 2MB
                    FileUpload::make('surat_kelulusan')
                        ->label('Surat Kelulusan')
                        ->acceptedFileTypes(['application/pdf', 'image/*']) // PDF atau Gambar
                        ->disk('public')
                        ->directory('dokumen-siswa/surat-kelulusan')
                        ->visibility('public')
                        ->nullable()
                        ->maxSize(5120), // Max 5MB
                    FileUpload::make('akta_kelahiran')
                        ->label('Akta Kelahiran')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->disk('public')
                        ->directory('dokumen-siswa/akta-kelahiran')
                        ->visibility('public')
                        ->nullable()
                        ->maxSize(5120),
                    FileUpload::make('kartu_keluarga')
                        ->label('Kartu Keluarga')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->disk('public')
                        ->directory('dokumen-siswa/kartu-keluarga')
                        ->visibility('public')
                        ->nullable()
                        ->maxSize(5120),
                    Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options(['laki-laki' => 'Laki-laki', 'perempuan' => 'Perempuan'])
                        ->required()
                        ->placeholder('-- Pilih jenis kelamin --'),
                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->nullable()
                        ->maxLength(100) // Sesuaikan dengan DB
                        ->placeholder('Kota/Kabupaten tempat lahir'),
                    DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->nullable()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->placeholder('Pilih tanggal'),
                    Textarea::make('alamat') // Ganti dengan Textarea untuk alamat
                        ->label('Alamat Lengkap')
                        ->nullable()
                        ->rows(3) // Tinggi default 3 baris
                        ->maxLength(65535) // Text field biasanya lebih besar
                        ->placeholder('Alamat lengkap siswa'),
                    TextInput::make('nomor_hp_siswa')
                        ->label('Nomor Telepon Siswa')
                        ->nullable()
                        ->tel() // Validasi sebagai nomor telepon
                        ->maxLength(20) // Sesuaikan dengan DB
                        ->placeholder('0812xxxx'),
                    TextInput::make('asal_sekolah')
                        ->label('Asal Sekolah')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Nama sekolah asal'),
                    TextInput::make('anak_ke')
                        ->label('Anak Ke-')
                        ->nullable()
                        ->numeric()
                        ->placeholder('Anak ke berapa'),
                    TextInput::make('jumlah_saudara')
                        ->label('Jumlah Saudara')
                        ->nullable()
                        ->numeric()
                        ->placeholder('Jumlah saudara kandung'),
                    TextInput::make('cita_cita')
                        ->label('Cita-cita')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Cita-cita siswa'),
                    TextInput::make('hobi')
                        ->label('Hobi')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Hobi siswa'),
                    TextInput::make('berat_badan')
                        ->label('Berat Badan (kg)')
                        ->nullable()
                        ->numeric()
                        ->step(0.1) // Untuk desimal
                        ->placeholder('Contoh: 45.5'),
                    TextInput::make('tinggi_badan')
                        ->label('Tinggi Badan (cm)')
                        ->nullable()
                        ->numeric()
                        ->step(0.1)
                        ->placeholder('Contoh: 155.0'),
                    Textarea::make('riwayat_penyakit')
                        ->label('Riwayat Penyakit')
                        ->nullable()
                        ->rows(3)
                        ->maxLength(65535)
                        ->placeholder('Riwayat penyakit yang pernah diderita'),
                ])->columns(2), // Atur layout kolom untuk section ini

            Section::make('Data Orang Tua (Ayah)')
                ->description('Data detail mengenai Ayah calon siswa.')
                ->schema([
                    TextInput::make('nama_ayah')
                        ->label('Nama Ayah')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Nama lengkap ayah'),
                    TextInput::make('status_ayah')
                        ->label('Status Ayah')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Contoh: Hidup, Wafat'),
                    TextInput::make('tempat_lahir_ayah')
                        ->label('Tempat Lahir Ayah')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Kota/Kabupaten tempat lahir ayah'),
                    DatePicker::make('tanggal_lahir_ayah')
                        ->label('Tanggal Lahir Ayah')
                        ->nullable()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->placeholder('Pilih tanggal'),
                    TextInput::make('pendidikan_ayah')
                        ->label('Pendidikan Ayah')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Contoh: SMA, S1'),
                    TextInput::make('pekerjaan_ayah')
                        ->label('Pekerjaan Ayah')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Contoh: Wiraswasta, PNS'),
                    Select::make('penghasilan_ayah')
                        ->label('Penghasilan Ayah')
                        ->options([
                            '< 1 Juta' => '< 1 Juta',
                            '1 Juta - 2 Juta' => '1 Juta - 2 Juta',
                            '2 Juta - 5 Juta' => '2 Juta - 5 Juta',
                            '5 Juta - 10 Juta' => '5 Juta - 10 Juta',
                            '> 10 Juta' => '> 10 Juta',
                        ])
                        ->nullable()
                        ->placeholder('Pilih rentang penghasilan'),
                    TextInput::make('nomor_hp_ayah')
                        ->label('Nomor Telepon Ayah')
                        ->nullable()
                        ->tel()
                        ->maxLength(20)
                        ->placeholder('0812xxxx'),
                ])->columns(2),

            Section::make('Data Orang Tua (Ibu)')
                ->description('Data detail mengenai Ibu calon siswa.')
                ->schema([
                    TextInput::make('nama_ibu')
                        ->label('Nama Ibu')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Nama lengkap ibu'),
                    TextInput::make('status_ibu')
                        ->label('Status Ibu')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Contoh: Hidup, Wafat'),
                    TextInput::make('tempat_lahir_ibu')
                        ->label('Tempat Lahir Ibu')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Kota/Kabupaten tempat lahir ibu'),
                    DatePicker::make('tanggal_lahir_ibu')
                        ->label('Tanggal Lahir Ibu')
                        ->nullable()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->placeholder('Pilih tanggal'),
                    TextInput::make('pendidikan_ibu')
                        ->label('Pendidikan Ibu')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Contoh: SMA, S1'),
                    TextInput::make('pekerjaan_ibu')
                        ->label('Pekerjaan Ibu')
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('Contoh: Ibu Rumah Tangga, PNS'),
                    Select::make('penghasilan_ibu')
                        ->label('Penghasilan Ibu')
                        ->options([
                            '< 1 Juta' => '< 1 Juta',
                            '1 Juta - 2 Juta' => '1 Juta - 2 Juta',
                            '2 Juta - 5 Juta' => '2 Juta - 5 Juta',
                            '5 Juta - 10 Juta' => '5 Juta - 10 Juta',
                            '> 10 Juta' => '> 10 Juta',
                        ])
                        ->nullable()
                        ->placeholder('Pilih rentang penghasilan'),
                    TextInput::make('nomor_hp_ibu')
                        ->label('Nomor Telepon Ibu')
                        ->nullable()
                        ->tel()
                        ->maxLength(20)
                        ->placeholder('0812xxxx'),
                ])->columns(2),

            Section::make('Status Pendaftaran')
                ->description('Pengaturan status persetujuan pendaftaran calon siswa.')
                ->schema([
                    Select::make('status')
                        ->label('Status Pendaftaran')
                        ->options([
                            'menunggu' => 'Menunggu',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak'
                        ])
                        ->required()
                        ->default('menunggu')
                        ->columnSpanFull()
                        ->placeholder('Pilih status'),
                    Select::make('disetujui_oleh_user_id')
                        ->label('Disetujui Oleh')
                        ->options(User::pluck('name', 'id'))
                        ->nullable()
                        ->visible(fn (string $operation): bool => $operation === 'edit') // Hanya terlihat saat edit
                        ->placeholder('Pilih petugas'),
                    DateTimePicker::make('tanggal_persetujuan')
                        ->label('Tanggal Persetujuan')
                        ->nullable()
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->visible(fn (string $operation): bool => $operation === 'edit') // Hanya terlihat saat edit
                        ->placeholder('Waktu persetujuan'),
                ])->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gelombang.nama')->label('Gelombang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_pendaftaran')
                    ->label('No. Pendaftaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('images/pp/default.jpg')), // Default image jika kosong
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan secara default
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->label('Tgl Lahir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nama_orang_tua')
                    ->label('Nama Ortu')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nomor_telepon_orang_tua')
                    ->label('Telp Ortu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_pendaftaran')
                    ->label('Tgl Pendaftaran')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disetujui_oleh_user.name')
                    ->label('Disetujui Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_persetujuan')
                    ->label('Tgl Persetujuan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gelombang_id')
                    ->label('Gelombang')
                    ->options(Gelombang::pluck('nama', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pendaftaran')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak'
                    ]),
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan'
                    ]),
                Tables\Filters\Filter::make('tanggal_pendaftaran')
                    ->form([
                        DatePicker::make('tanggal_mulai')
                            ->placeholder('Dari Tanggal'),
                        DatePicker::make('tanggal_akhir')
                            ->placeholder('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_mulai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pendaftaran', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_akhir'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pendaftaran', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
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
            'index' => Pages\ListCalonSiswas::route('/'),
            'create' => Pages\CreateCalonSiswa::route('/create'),
            'edit' => Pages\EditCalonSiswa::route('/{record}/edit'),
        ];
    }

    // Penanganan izin berbasis peran (role permissions)
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
                'viewAny' => true, 'create' => true, 'edit' => true, 'delete' => true,
            ],
            User::ROLE_KEPSEK => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
            User::ROLE_TATA_USAHA => [
                'viewAny' => true, 'create' => true, 'edit' => true, 'delete' => true,
            ],
            // Asumsi ROLE_STAFF_PPDB adalah peran baru yang akan mengelola calon siswa
            User::ROLE_STAFF_PPDB => [
                'viewAny' => true, 'create' => true, 'edit' => true, 'delete' => false,
            ],
            User::ROLE_GURU => [ // Guru tidak bisa melihat/mengelola calon siswa
                'viewAny' => false, 'create' => false, 'edit' => false, 'delete' => false,
            ],
            // Tambahkan peran lain jika diperlukan
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }
}