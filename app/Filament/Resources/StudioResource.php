<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudioResource\Pages;
use App\Models\Bioskop;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Studio';

    protected static ?string $modelLabel = 'Studio';

    protected static ?string $pluralModelLabel = 'Studio';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'nama';

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
                Forms\Components\Section::make('Informasi Studio')
                    ->description('Data utama studio/ruang pemutaran.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('bioskop_id')
                            ->label('Bioskop')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Bioskop::query()
                                ->orderBy('kota')
                                ->orderBy('nama')
                                ->get()
                                ->mapWithKeys(fn($b) => [$b->id => $b->nama . ' — ' . $b->kota]))
                            ->helperText('Pilih bioskop tempat studio ini berada.')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Studio')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->placeholder('Studio 1'),

                        Forms\Components\Select::make('tipe')
                            ->label('Tipe Studio')
                            ->required()
                            ->options([
                                'reguler' => 'Reguler',
                                'premiere' => 'Premiere',
                                'imax' => 'IMAX',
                                'velvet' => 'Velvet',
                                'sweetbox' => 'Sweetbox',
                                '4dx' => '4DX',
                            ])
                            ->default('reguler'),

                        Forms\Components\TextInput::make('kapasitas')
                            ->label('Kapasitas')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->suffix('kursi')
                            ->helperText('Jumlah total kursi di studio.'),

                        Forms\Components\KeyValue::make('layout_kursi')
                            ->label('Layout Kursi')
                            ->helperText('Baris dan kolom kursi (contoh: baris=10, kolom=15).')
                            ->keyLabel('Parameter')
                            ->valueLabel('Nilai')
                            ->editableKeys(false)
                            ->default(['baris' => 10, 'kolom' => 15])
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->striped()
            ->deferLoading()
            ->extremePaginationLinks()
            ->emptyStateHeading('Belum ada data studio')
            ->emptyStateDescription('Tambahkan studio pertama dengan menekan tombol "Tambah Studio" di atas.')
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Studio')
                    ->searchable(query: fn(Builder $query, string $search) => $query->where('nama', 'like', '%' . $search . '%'))
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('bioskop.nama')
                    ->label('Bioskop')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record?->bioskop?->kota),

                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'imax' => 'danger',
                        'premiere' => 'warning',
                        'velvet' => 'success',
                        'sweetbox' => 'pink',
                        '4dx' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('kapasitas')
                    ->label('Kursi')
                    ->sortable()
                    ->alignment('center')
                    ->suffix(' kursi'),

                Tables\Columns\TextColumn::make('kursis_count')
                    ->label('Tersedia')
                    ->counts('kursis')
                    ->sortable()
                    ->alignment('center')
                    ->placeholder('—'),

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
                Tables\Filters\SelectFilter::make('bioskop_id')
                    ->label('Bioskop')
                    ->options(fn() => Bioskop::query()
                        ->orderBy('kota')
                        ->orderBy('nama')
                        ->get()
                        ->mapWithKeys(fn($b) => [$b->id => $b->nama . ' — ' . $b->kota]))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'reguler' => 'Reguler',
                        'premiere' => 'Premiere',
                        'imax' => 'IMAX',
                        'velvet' => 'Velvet',
                        'sweetbox' => 'Sweetbox',
                        '4dx' => '4DX',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record))
                    ->modalHeading('Hapus Studio')
                    ->modalDescription('Apakah Anda yakin ingin menghapus studio ini? Data dapat dipulihkan dari Sampah.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Studio berhasil dihapus.'),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn($record) => static::canRestore($record))
                    ->successNotificationTitle('Studio berhasil dipulihkan.'),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn($record) => static::canForceDelete($record))
                    ->modalHeading('Hapus Permanen')
                    ->modalDescription('Tindakan ini tidak dapat dibatalkan. Yakin menghapus permanen?')
                    ->modalSubmitActionLabel('Ya, Hapus Permanen')
                    ->successNotificationTitle('Studio dihapus permanen.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::canDeleteAny())
                        ->modalHeading('Hapus Studio Terpilih')
                        ->modalDescription('Studio yang dipilih akan dipindahkan ke Sampah.')
                        ->successNotificationTitle('Studio berhasil dihapus.'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn() => static::canRestoreAny())
                        ->successNotificationTitle('Studio berhasil dipulihkan.'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn() => static::canForceDeleteAny())
                        ->modalHeading('Hapus Permanen Massal')
                        ->modalDescription('Tindakan ini tidak dapat dibatalkan.')
                        ->successNotificationTitle('Studio dihapus permanen.'),
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
            'index' => Pages\ListStudios::route('/'),
            'create' => Pages\CreateStudio::route('/create'),
            'view' => Pages\ViewStudio::route('/{record}'),
            'edit' => Pages\EditStudio::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function sanitizeFormData(array $data): array
    {
        if (isset($data['nama']) && is_string($data['nama'])) {
            $data['nama'] = trim(strip_tags($data['nama']));
        }

        if (isset($data['tipe']) && is_string($data['tipe'])) {
            $data['tipe'] = trim(strip_tags($data['tipe']));
        }

        return $data;
    }
}
