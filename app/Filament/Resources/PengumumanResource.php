<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumumanResource\Pages;
use App\Models\Pengumuman;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Pengumuman';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Pengumuman')
                    ->columnSpanFull()
                    ->placeholder('Masukkan judul pengumuman'),
                Textarea::make('konten')
                    ->required()
                    ->columnSpanFull()
                    ->rows(5)
                    ->label('Isi Pengumuman')
                    ->placeholder('Tulis isi pengumuman di sini.'),
                Select::make('target_peran')
                    ->options([
                        'semua' => 'Semua Pengguna',
                        'siswa' => 'Siswa',
                        'guru' => 'Guru',
                        'tata_usaha' => 'Tata Usaha',
                        'staff_ppdb' => 'Staff PPDB', // Sesuai enum 'role' di tabel users
                        'admin' => 'Admin',
                        'staff' => 'Staff Lainnya',
                    ])
                    ->nullable()
                    ->label('Target Peran')
                    ->placeholder('Pilih peran yang dituju (opsional)'),
                DateTimePicker::make('tanggal_publikasi')
                    ->nullable()
                    ->withoutSeconds()
                    ->default(now()) // Default ke waktu sekarang
                    ->label('Tanggal & Waktu Publikasi')
                    ->placeholder('Pilih tanggal dan waktu publikasi'),
                Forms\Components\Hidden::make('diposting_oleh_user_id')
                    ->default(fn() => Auth::user()->id),
            ]);
    }
    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isSiswaOrGuru = in_array($user->role, [User::ROLE_SISWA, User::ROLE_GURU]);

        $table = $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->label('Judul'),
                Tables\Columns\TextColumn::make('konten')
                    ->limit(70)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Isi Pengumuman'),
                Tables\Columns\TextColumn::make('dipostingOleh.name')
                    ->label('Diposting Oleh')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_peran')
                    ->label('Target')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_publikasi')
                    ->dateTime()
                    ->sortable()
                    ->dateTime('M d, Y H:i') 
                    ->label('Waktu Publikasi'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
            ])
            ->filters([
                SelectFilter::make('target_peran')
                    ->options([
                        'semua' => 'Semua Pengguna',
                        'siswa' => 'Siswa',
                        'guru' => 'Guru',
                        'tata_usaha' => 'Tata Usaha',
                        'staff_ppdb' => 'Staff PPDB',
                        'admin' => 'Admin',
                        'staff' => 'Staff Lainnya',
                    ])
                    ->placeholder('Semua Target Peran'),
                Tables\Filters\Filter::make('tanggal_publikasi')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('Dari Tanggal Publikasi'),
                        Forms\Components\DatePicker::make('published_until')
                            ->label('Sampai Tanggal Publikasi'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_publikasi', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_publikasi', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->hidden(fn($record) => !static::canEdit($record)),
                Tables\Actions\DeleteAction::make()->hidden(fn($record) => !static::canEdit($record)),
            ]);

        // Hanya tambahkan bulk actions jika bukan siswa atau guru
        if (!$isSiswaOrGuru) {
            $table = $table->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
        }

        return $table;
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
            'index' => Pages\ListPengumuman::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
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
            User::ROLE_KEPSEK => [
                'viewAny' => true,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
            User::ROLE_STAFF_PPDB => [
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

        return $rolePermissions[$user->role][$action] ?? false;
    }
}
