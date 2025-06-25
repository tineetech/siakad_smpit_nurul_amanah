<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PenetapanSppsResource\Pages;
use App\Filament\Resources\PenetapanSppsResource\RelationManagers;
use App\Models\PenetapanSpps;
use App\Models\PengaturanSpp;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PenetapanSppsResource extends Resource
{
    protected static ?string $model = PenetapanSpps::class;

    protected static ?string $navigationGroup = 'POS SPP';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Penetapan SPP';
    protected static ?string $pluralLabel = 'Penetapan SPP';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_ids')
                    ->label('Siswa')
                    ->multiple()
                    ->options(Siswa::pluck('nama_lengkap', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih satu atau lebih siswa')
                    ->visible(fn (Page $livewire) => $livewire instanceof \App\Filament\Resources\PenetapanSppsResource\Pages\CreatePenetapanSpps),
                Forms\Components\Select::make('pengaturan_spp_ids')
                    ->label('Pengaturan SPP')
                    ->multiple()
                    ->options(PengaturanSpp::pluck('nama', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih satu atau lebih pengaturan SPP')
                    ->visible(fn (Page $livewire) => $livewire instanceof \App\Filament\Resources\PenetapanSppsResource\Pages\CreatePenetapanSpps),

                Forms\Components\Select::make('siswa_id_edit')
                    ->label('Siswa')
                    ->options(Siswa::pluck('nama_lengkap', 'id'))
                    ->searchable()
                    ->required()
                    ->disabled()
                    ->placeholder('Pilih satu siswa')
                    ->visible(fn (Page $livewire) => $livewire instanceof \App\Filament\Resources\PenetapanSppsResource\Pages\EditPenetapanSpps),
                Forms\Components\Select::make('pengaturan_spp_id_edit')
                    ->label('Pengaturan SPP')
                    ->options(PengaturanSpp::pluck('nama', 'id'))
                    ->searchable()
                    ->required()
                    ->disabled()
                    ->placeholder('Pilih satu pengaturan SPP')
                    ->visible(fn (Page $livewire) => $livewire instanceof \App\Filament\Resources\PenetapanSppsResource\Pages\EditPenetapanSpps),

                Forms\Components\DatePicker::make('tanggal_jatuh_tempo')
                    ->label('Tanggal Jatuh Tempo')
                    ->required()
                    ->minDate(now())
                    ->placeholder('Pilih tanggal jatuh tempo')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')->label('Siswa'),
                Tables\Columns\TextColumn::make('pengaturanSpp.nama')->label('Nama SPP'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'dibayar_setengah' => 'warning',
                    'lunas' => 'success',
                    'belum_dibayar' => 'danger',
                }),
                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')->date(),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenetapanSpps::route('/'),
            'create' => Pages\CreatePenetapanSpps::route('/create'),
            'edit' => Pages\EditPenetapanSpps::route('/{record}/edit'),
        ];
    }

   public static function syncPenetapanSpp(array $data): void
    {
        $siswaIds = $data['siswa_ids'] ?? [];
        $pengaturanIds = $data['pengaturan_spp_ids'] ?? [];
        $tanggalJatuhTempo = $data['tanggal_jatuh_tempo'] ?? null;

        if (!$tanggalJatuhTempo || empty($siswaIds) || empty($pengaturanIds)) {
            Notification::make()
                ->title('Data tidak lengkap')
                ->body('Pastikan semua field terisi.')
                ->danger()
                ->send();
            return;
        }

        foreach ($siswaIds as $siswaId) {
            foreach ($pengaturanIds as $pengaturanId) {
                PenetapanSpps::create([
                    'siswa_id' => $siswaId,
                    'pengaturan_spp_id' => $pengaturanId,
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                    'status' => 'belum_dibayar',
                ]);
            }
        }

        Notification::make()
            ->title('Berhasil')
            ->body('Penetapan SPP berhasil dibuat.')
            ->success()
            ->send();
    }


}
