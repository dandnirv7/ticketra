<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BioskopResource\Pages;
use App\Models\Bioskop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BioskopResource extends Resource
{
    protected static ?string $model = Bioskop::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Bioskop';

    protected static ?string $modelLabel = 'Bioskop';

    protected static ?string $pluralModelLabel = 'Bioskop';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 2;

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
                Forms\Components\Section::make('Informasi Bioskop')
                    ->description('Data utama bioskop yang akan ditampilkan ke pengguna.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Bioskop')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->placeholder('CGV Grand Indonesia')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('kota')
                            ->label('Kota')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->placeholder('Jakarta Selatan'),

                        Forms\Components\TimePicker::make('jam_buka')
                            ->label('Jam Buka')
                            ->required()
                            ->native(false)
                            ->displayFormat('H:i')
                            ->default('10:00'),

                        Forms\Components\TimePicker::make('jam_tutup')
                            ->label('Jam Tutup')
                            ->required()
                            ->native(false)
                            ->displayFormat('H:i')
                            ->default('23:00')
                            ->rules(['after:jam_buka'])
                            ->helperText('Harus setelah jam buka.'),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->required()
                            ->rows(4)
                            ->minLength(5)
                            ->maxLength(500)
                            ->placeholder('Jl. M.H. Thamrin No.1, Jakarta Pusat')
                            ->columnSpan(2),

                        Forms\Components\CheckboxList::make('fasilitas')
                            ->label('Fasilitas')
                            ->helperText('Pilih fasilitas yang tersedia di bioskop.')
                            ->options([
                                'parkir' => 'Parkir',
                                'food_court' => 'Food Court',
                                'mushola' => 'Mushola',
                                'wifi' => 'WiFi',
                                'atm' => 'ATM',
                                'akses_disabilitas' => 'Akses Disabilitas',
                                'lift' => 'Lift',
                                'escalator' => 'Escalator',
                            ])
                            ->columns(2)
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
            ->emptyStateHeading('Belum ada data bioskop')
            ->emptyStateDescription('Tambahkan bioskop pertama dengan menekan tombol "Tambah Bioskop" di atas.')
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Bioskop')
                    ->searchable(query: fn(Builder $query, string $search) => $query->where('nama', 'like', '%' . $search . '%'))
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('kota')
                    ->label('Kota')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('studios_count')
                    ->label('Studio')
                    ->counts('studios')
                    ->sortable()
                    ->alignment('center')
                    ->formatStateUsing(fn($state) => $state . ' studio')
                    ->placeholder('0'),

                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(40)
                    ->tooltip(fn($record) => $record?->alamat),

                Tables\Columns\TextColumn::make('jam_buka')
                    ->label('Jam Buka')
                    ->time('H:i')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('jam_tutup')
                    ->label('Jam Tutup')
                    ->time('H:i')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('fasilitas')
                    ->label('Fasilitas')
                    ->formatStateUsing(fn($state) => is_array($state)
                        ? collect($state)->map(fn($f) => ucwords(str_replace('_', ' ', $f)))->implode(', ')
                        : '-')
                    ->limit(50)
                    ->tooltip(fn($record) => is_array($record?->fasilitas)
                        ? collect($record->fasilitas)->map(fn($f) => ucwords(str_replace('_', ' ', $f)))->implode(', ')
                        : '-')
                    ->placeholder('-'),

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
                Tables\Filters\SelectFilter::make('kota')
                    ->label('Kota')
                    ->options(fn() => Bioskop::query()
                        ->whereNotNull('kota')
                        ->distinct()
                        ->pluck('kota', 'kota')
                        ->filter()
                        ->toArray())
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record))
                    ->modalHeading('Hapus Bioskop')
                    ->modalDescription('Apakah Anda yakin ingin menghapus bioskop ini? Data dapat dipulihkan dari Sampah.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Bioskop berhasil dihapus.'),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn($record) => static::canRestore($record))
                    ->successNotificationTitle('Bioskop berhasil dipulihkan.'),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn($record) => static::canForceDelete($record))
                    ->modalHeading('Hapus Permanen')
                    ->modalDescription('Tindakan ini tidak dapat dibatalkan. Yakin menghapus permanen?')
                    ->modalSubmitActionLabel('Ya, Hapus Permanen')
                    ->successNotificationTitle('Bioskop dihapus permanen.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::canDeleteAny())
                        ->modalHeading('Hapus Bioskop Terpilih')
                        ->modalDescription('Bioskop yang dipilih akan dipindahkan ke Sampah.')
                        ->successNotificationTitle('Bioskop berhasil dihapus.'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn() => static::canRestoreAny())
                        ->successNotificationTitle('Bioskop berhasil dipulihkan.'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn() => static::canForceDeleteAny())
                        ->modalHeading('Hapus Permanen Massal')
                        ->modalDescription('Tindakan ini tidak dapat dibatalkan.')
                        ->successNotificationTitle('Bioskop dihapus permanen.'),
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
            'index' => Pages\ListBioskops::route('/'),
            'create' => Pages\CreateBioskop::route('/create'),
            'view' => Pages\ViewBioskop::route('/{record}'),
            'edit' => Pages\EditBioskop::route('/{record}/edit'),
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
        foreach (['nama', 'kota'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim(strip_tags($data[$field]));
            }
        }

        if (isset($data['alamat']) && is_string($data['alamat'])) {
            $data['alamat'] = trim(strip_tags($data['alamat']));
        }

        if (array_key_exists('fasilitas', $data) && !is_array($data['fasilitas'])) {
            $data['fasilitas'] = [];
        }

        return $data;
    }
}
