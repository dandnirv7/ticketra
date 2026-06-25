<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalTayangResource\Pages;
use App\Models\Film;
use App\Models\JadwalTayang;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class JadwalTayangResource extends Resource
{
    protected static ?string $model = JadwalTayang::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Tayang';

    protected static ?string $modelLabel = 'Jadwal Tayang';

    protected static ?string $pluralModelLabel = 'Jadwal Tayang';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    private static function userHasAnyRole(array $roles): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        if (method_exists($user, 'hasAnyRole')) {
            return (bool) $user->hasAnyRole($roles);
        }

        if (isset($user->role)) {
            return in_array($user->role, $roles, true);
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        return static::userHasAnyRole(['admin', 'editor']);
    }

    public static function canCreate(): bool
    {
        return static::userHasAnyRole(['admin', 'editor']);
    }

    public static function canEdit($record): bool
    {
        return static::userHasAnyRole(['admin', 'editor']);
    }

    public static function canDelete($record): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function canDeleteAny(): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function canForceDelete($record): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function canForceDeleteAny(): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function canRestore($record): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function canRestoreAny(): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jadwal')
                    ->description('Atur jadwal tayang film di studio tertentu.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('film_id')
                            ->label('Film')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Film::query()
                                ->orderBy('judul')
                                ->get()
                                ->mapWithKeys(fn($f) => [$f->id => $f->judul]))
                            ->columnSpan(2),

                        Forms\Components\Select::make('studio_id')
                            ->label('Studio')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Studio::query()
                                ->with('bioskop')
                                ->orderBy('nama')
                                ->get()
                                ->mapWithKeys(fn($s) => [$s->id => $s->nama . ' — ' . ($s->bioskop?->nama ?? '-')]))
                            ->helperText('Pilih studio tempat film diputar.')
                            ->columnSpan(2),

                        Forms\Components\DateTimePicker::make('waktu_mulai')
                            ->label('Waktu Mulai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y H:i')
                            ->minutesStep(5)
                            ->helperText('Waktu mulai tayang.'),

                        Forms\Components\DateTimePicker::make('waktu_selesai')
                            ->label('Waktu Selesai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y H:i')
                            ->minutesStep(5)
                            ->after('waktu_mulai')
                            ->helperText('Harus setelah waktu mulai.'),

                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Tiket')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(9999999)
                            ->prefix('Rp')
                            ->helperText('Harga tiket per kursi.'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'terjadwal' => 'Terjadwal',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('terjadwal'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_mulai', 'asc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->striped()
            ->deferLoading()
            ->extremePaginationLinks()
            ->emptyStateHeading('Belum ada jadwal tayang')
            ->emptyStateDescription('Tambahkan jadwal tayang baru dengan menekan tombol di atas.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->columns([
                Tables\Columns\TextColumn::make('film.judul')
                    ->label('Film')
                    ->searchable(query: fn(Builder $query, string $search) => $query->whereHas('film', fn($q) => $q->where('judul', 'like', '%' . $search . '%')))
                    ->sortable()
                    ->weight('medium')
                    ->limit(25)
                    ->tooltip(fn($record) => $record?->film?->judul),

                Tables\Columns\TextColumn::make('studio.nama')
                    ->label('Studio')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record?->studio?->bioskop?->nama),

                Tables\Columns\TextColumn::make('waktu_mulai')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('waktu_selesai')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->sortable()
                    ->money('IDR')
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'terjadwal' => 'success',
                        'selesai' => 'gray',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Sampah')
                    ->falseLabel('Aktif'),
                Tables\Filters\SelectFilter::make('film_id')
                    ->label('Film')
                    ->options(fn() => Film::query()
                        ->orderBy('judul')
                        ->pluck('judul', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'terjadwal' => 'Terjadwal',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record))
                    ->modalHeading('Hapus Jadwal')
                    ->modalDescription('Apakah Anda yakin ingin menghapus jadwal tayang ini? Data dapat dipulihkan dari Sampah.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Jadwal berhasil dihapus.'),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn($record) => static::canRestore($record))
                    ->successNotificationTitle('Jadwal berhasil dipulihkan.'),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn($record) => static::canForceDelete($record))
                    ->modalHeading('Hapus Permanen')
                    ->modalDescription('Tindakan ini tidak dapat dibatalkan. Yakin menghapus permanen?')
                    ->modalSubmitActionLabel('Ya, Hapus Permanen')
                    ->successNotificationTitle('Jadwal dihapus permanen.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::canDeleteAny())
                        ->modalHeading('Hapus Jadwal Terpilih')
                        ->modalDescription('Jadwal yang dipilih akan dipindahkan ke Sampah.')
                        ->successNotificationTitle('Jadwal berhasil dihapus.'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn() => static::canRestoreAny())
                        ->successNotificationTitle('Jadwal berhasil dipulihkan.'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn() => static::canForceDeleteAny())
                        ->modalHeading('Hapus Permanen Massal')
                        ->modalDescription('Tindakan ini tidak dapat dibatalkan.')
                        ->successNotificationTitle('Jadwal dihapus permanen.'),
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
            'index' => Pages\ListJadwalTayangs::route('/'),
            'create' => Pages\CreateJadwalTayang::route('/create'),
            'view' => Pages\ViewJadwalTayang::route('/{record}'),
            'edit' => Pages\EditJadwalTayang::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
