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

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Manajemen Informasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Pengumuman')
                    ->placeholder('Masukkan judul pengumuman'),
                Textarea::make('konten')
                    ->required()
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
                    ->default(now()) // Default ke waktu sekarang
                    ->label('Tanggal & Waktu Publikasi')
                    ->placeholder('Pilih tanggal dan waktu publikasi'),
                Forms\Components\Hidden::make('diposting_oleh_user_id')
                    ->default(fn () => Auth::user()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_publikasi', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_publikasi', '<=', $date),
                            );
                    }),
            ])
            ->actions([
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
            'index' => Pages\ListPengumumen::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }

    // Mengambil user ID yang memposting (sudah diatur di form, ini redundant tapi bisa jadi fallback)
    // public static function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['diposting_oleh_user_id'] = auth()->id();
    //     return $data;
    // }

 
}