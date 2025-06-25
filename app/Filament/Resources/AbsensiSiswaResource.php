<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiSiswaResource\Pages;
use App\Models\AbsensiSiswa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;

class AbsensiSiswaResource extends Resource
{
    protected static ?string $model = AbsensiSiswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationLabel = 'Absensi Siswa';
    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('tanggal_absensi')
                ->required()
                ->default(now()),

            Forms\Components\TimePicker::make('waktu_absensi')
                ->required()
                ->default(now()),

            Forms\Components\Select::make('siswa_id')
                ->relationship('siswa', 'nama_lengkap')
                ->searchable()
                ->required()
                ->hidden(fn() => Auth::user()->role === User::ROLE_SISWA),

            Forms\Components\Select::make('status_kehadiran')
                ->options([
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpha' => 'Alpha',
                ])
                ->required(),

            Forms\Components\Select::make('mode_absensi')
                ->options([
                    'manual' => 'Manual',
                    'lainnya' => 'Lainnya',
                ])
                ->required()
                ->default('manual')
                ->hidden(fn() => Auth::user()->role === User::ROLE_SISWA),

            Forms\Components\Textarea::make('catatan')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_absensi')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('waktu_absensi')
                    ->time(),

                Tables\Columns\TextColumn::make('siswa.kelas.nama')
                    ->label('Kelas')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->hidden(fn() => Auth::user()->role === User::ROLE_SISWA),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'info',
                        'alpha' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('mode_absensi')
                    ->label('Mode')
                    ->hidden(fn() => Auth::user()->role === User::ROLE_SISWA),
            ])
            ->filters([
                SelectFilter::make('kelas')
                    ->relationship('siswa.kelas', 'nama')
                    ->hidden(fn() => Auth::user()->role === User::ROLE_SISWA)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('siswa')
                    ->relationship('siswa', 'nama_lengkap')
                    ->hidden(fn() => Auth::user()->role === User::ROLE_SISWA)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status_kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ]),

                Filter::make('tanggal_absensi')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_absensi', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_absensi', '<=', $date),
                            );
                    }),

                Filter::make('my_attendance')
                    ->label('Only My Attendance')
                    ->query(fn(Builder $query) => $query->where('siswa_id', Siswa::where('user_id', Auth::id())->first()?->id))
                    ->hidden(fn() => Auth::user()->role !== User::ROLE_SISWA),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => !static::canEdit($record)),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->hidden(fn() => !static::hasPermission('delete')),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_TATA_USAHA])) {
            return $query;
        }

        if ($user->role === User::ROLE_GURU) {
            // Guru can see all students' attendance but can filter by class
            return $query->orderBy('tanggal_absensi', 'desc');
        }

        if ($user->role === User::ROLE_SISWA) {
            $siswa = Siswa::where('user_id', $user->id)->first();
            if ($siswa) {
                // Show only the student's own attendance records
                return $query->where('siswa_id', $siswa->id)
                    ->orderBy('tanggal_absensi', 'desc');
            }
            return $query->whereRaw('1=0');
        }

        return $query->whereRaw('1=0');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiSiswas::route('/'),
            'create' => Pages\CreateAbsensiSiswa::route('/create'),
            'edit' => Pages\EditAbsensiSiswa::route('/{record}/edit')
        ];
    }

    public static function canViewAny(): bool
    {
        return self::hasPermission('viewAny');
    }

    public static function canCreate(): bool
    {
        return self::hasPermission('create');
    }

    public static function canEdit(Model $record): bool
    {
        return self::hasPermission('edit');
    }

    public static function canDelete(Model $record): bool
    {
        return self::hasPermission('delete');
    }

    protected static function hasPermission(string $action): bool
    {
        $user = Auth::user();

        if (!$user) return false;

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
                'create' => true,
                'edit' => true,
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