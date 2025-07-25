<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranSppResource\Pages;
use App\Filament\Resources\PembayaranSppResource\RelationManagers;
use App\Models\PembayaranSpp;
use App\Models\PenetapanSpps;
use App\Models\Siswa;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PembayaranSppResource extends Resource
{
    protected static ?string $model = PembayaranSpp::class;
    protected static ?string $navigationGroup = 'POS SPP';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $label = 'Pembayaran SPP';
    protected static ?string $pluralLabel = 'Pembayaran SPP';

    public static function form(Form $form): Form
    {
        return $form->schema([

             Select::make('penetapan_spp_id')
                ->label('Tagihan/Penetapan SPP')
                ->options(
                    PenetapanSpps::with('pengaturanSpp', 'siswa')
                        ->where('status', '!=', 'lunas')
                        ->get()
                        ->mapWithKeys(fn ($item) => [
                            $item->id => $item->siswa->nama_lengkap . ' - ' . $item->pengaturanSpp?->nama . ' - ' . number_format((float) $item->pengaturanSpp?->jumlah),
                        ])
                )
                ->required()
                ->searchable()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $penetapan = PenetapanSpps::with('siswa')->find($state);
                    if ($penetapan) {
                        $set('siswa_id', $penetapan->siswa_id); // isi otomatis
                    }
                }),

            Select::make('siswa_id')
                ->label('Siswa')
                ->options(Siswa::pluck('nama_lengkap', 'id'))
                ->disabled()
                ->dehydrated(true)
                ->required(),

            Forms\Components\TextInput::make('jumlah_dibayar')
                ->label('Jumlah Dibayar')
                ->numeric()
                ->required(),

            Forms\Components\DatePicker::make('tanggal_pembayaran')
                ->label('Tanggal Pembayaran')
                ->default(now())
                ->required(),

            Forms\Components\Select::make('metode_pembayaran')
                ->options([
                    'tunai' => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS',
                ])
                ->reactive()
                ->required(),
            
            FileUpload::make('bukti_tf')
                ->label('Bukti Transfer (JIKA TF)')
                ->directory('bukti-transfer-spp')
                ->visibility('public')
                ->disk('public')
                ->nullable()
                ->image(),

            Forms\Components\Select::make('status')
                ->options([
                    'lunas' => 'Lunas',
                    'sebagian_dibayar' => 'Sebagian Dibayar',
                    'belum_dibayar' => 'Belum Dibayar',
                ])
                ->default('lunas')
                ->required(),

            Forms\Components\Select::make('teller_user_id')
                ->label('Petugas/Teller')
                ->options(User::where('role', 'tata_usaha')->pluck('name', 'id'))
                ->required(),

            Forms\Components\Textarea::make('catatan')
                ->label('Catatan')
                ->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')->label('Siswa')->searchable(),
                Tables\Columns\TextColumn::make('jumlah_dibayar')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran')->label('Metode'),
                Tables\Columns\TextColumn::make('penetapan.status')->label('Status')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'sebagian_dibayar' => 'warning',
                    'lunas' => 'success',
                    'belum_dibayar' => 'danger',
                }),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')->date(),
                Tables\Columns\TextColumn::make('teller.name')->label('Teller'),
            ])
            ->filters([
                SelectFilter::make('siswa_id')
                    ->label('Siswa')
                    ->options(Siswa::pluck('nama_lengkap', 'id'))
                    ->placeholder('Semua Siswa'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('tandaiLunas')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check')
                    ->action(fn($record) => $record->penetapan?->update(['status' => 'lunas']))
                    ->requiresConfirmation()
                    ->color('success')
                    ->visible(fn($record) => $record->penetapan && $record->penetapan->status !== 'lunas'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayaranSpps::route('/'),
            'create' => Pages\CreatePembayaranSpp::route('/create'),
            'edit' => Pages\EditPembayaranSpp::route('/{record}/edit'),
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
            User::ROLE_SISWA => [
                'viewAny' => false,
                'create' => false,
                'edit' => false,
                'delete' => false,
            ],
        ];

        return $rolePermissions[$user->role][$action] ?? false;
    }
}
