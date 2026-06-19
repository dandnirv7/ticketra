<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FilmResource\Pages;
use App\Models\Film;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FilmResource extends Resource
{
    protected static ?string $model = Film::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationLabel = 'Film';

    protected static ?string $modelLabel = 'Film';

    protected static ?string $pluralModelLabel = 'Film';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'judul';

    /**
     * Pembatasan akses berbasis role. Sesuaikan helper role dengan
     * implementasi aplikasi (mis. Spatie: hasAnyRole, atau kolom 'role').
     */
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

        // Fallback: jika tidak ada helper role/kolom role, izinkan semua user terautentikasi.
        // Segera ganti dengan sistem role yang sebenarnya (Spatie/kolom role) untuk produksi.
        return true;
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
                Forms\Components\Section::make('Informasi Film')
                    ->description('Data utama film yang akan ditampilkan ke pengguna.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->rule(
                                Rule::unique('films', 'judul')
                                    ->whereNull('deleted_at')
                                    ->ignore(request()->route('record'))
                            )
                            ->validationMessages([
                                'unique' => 'Judul film sudah digunakan.',
                            ])
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('genre')
                            ->label('Genre')
                            ->required()
                            ->minLength(2)
                            ->maxLength(100)
                            ->placeholder('Contoh: Aksi, Drama, Komedi'),

                        Forms\Components\TextInput::make('durasi_menit')
                            ->label('Durasi')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(600)
                            ->suffix('menit')
                            ->helperText('Durasi film dalam menit (1 - 600).'),

                        Forms\Components\TextInput::make('rating')
                            ->label('Rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->step(0.1)
                            ->default(0)
                            ->helperText('Nilai antara 0.0 - 10.0.'),

                        Forms\Components\DatePicker::make('tanggal_rilis')
                            ->label('Tanggal Rilis')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->maxDate(now())
                            ->helperText('Tanggal rilis film, tidak boleh di masa depan.'),

                        Forms\Components\Toggle::make('sedang_tayang')
                            ->label('Sedang Tayang')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Aktifkan jika film sedang tayang.'),

                        Forms\Components\FileUpload::make('poster_url')
                            ->label('Poster')
                            ->disk('public')
                            ->directory('posters')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('2:3')
                            ->imageResizeTargetWidth(600)
                            ->imageResizeTargetHeight(900)
                            ->placeholder('Upload poster film')
                            ->helperText('File: jpg, png, webp. Maks: 5MB. Otomatis di-resize ke 600x900px.')
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('sinopsis')
                            ->label('Sinopsis')
                            ->required()
                            ->rows(5)
                            ->minLength(10)
                            ->maxLength(2000)
                            ->helperText('Minimal 10 karakter, maksimal 2000 karakter.')
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
            ->emptyStateHeading('Belum ada data film')
            ->emptyStateDescription('Tambahkan film pertama Anda dengan menekan tombol "Tambah Film" di atas.')
            ->emptyStateIcon('heroicon-o-film')
            ->columns([
                Tables\Columns\ImageColumn::make('poster_url')
                    ->label('Poster')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(asset('images/poster-placeholder.png'))
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                        'referrerpolicy' => 'no-referrer',
                    ]),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable(query: fn(Builder $query, string $search) => $query->where('judul', 'like', '%' . $search . '%'))
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record?->judul)
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('genre')
                    ->label('Genre')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->sortable()
                    ->suffix(' menit')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable()
                    ->alignment('center')
                    ->formatStateUsing(fn($state) => $state !== null ? number_format((float) $state, 1) . ' ⭐' : '-')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('sedang_tayang')
                    ->label('Tayang')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('tanggal_rilis')
                    ->label('Rilis')
                    ->date('d M Y')
                    ->sortable()
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
                Tables\Filters\SelectFilter::make('genre')
                    ->label('Genre')
                    ->options(fn() => Film::query()
                        ->whereNotNull('genre')
                        ->distinct()
                        ->pluck('genre', 'genre')
                        ->filter()
                        ->toArray())
                    ->searchable(),
                Tables\Filters\Filter::make('sedang_tayang')
                    ->label('Sedang Tayang')
                    ->toggle()
                    ->query(fn(Builder $query) => $query->where('sedang_tayang', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record))
                    ->modalHeading('Hapus Film')
                    ->modalDescription('Apakah Anda yakin ingin menghapus film ini? Data dapat dipulihkan dari Sampah.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Film berhasil dihapus.'),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn($record) => static::canRestore($record))
                    ->successNotificationTitle('Film berhasil dipulihkan.'),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn($record) => static::canForceDelete($record))
                    ->modalHeading('Hapus Permanen')
                    ->modalDescription('Tindakan ini tidak dapat dibatalkan. Yakin menghapus permanen?')
                    ->modalSubmitActionLabel('Ya, Hapus Permanen')
                    ->successNotificationTitle('Film dihapus permanen.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::canDeleteAny())
                        ->modalHeading('Hapus Film Terpilih')
                        ->modalDescription('Film yang dipilih akan dipindahkan ke Sampah.')
                        ->successNotificationTitle('Film berhasil dihapus.'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn() => static::canRestoreAny())
                        ->successNotificationTitle('Film berhasil dipulihkan.'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn() => static::canForceDeleteAny())
                        ->modalHeading('Hapus Permanen Massal')
                        ->modalDescription('Tindakan ini tidak dapat dibatalkan.')
                        ->successNotificationTitle('Film dihapus permanen.'),
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
            'index' => Pages\ListFilms::route('/'),
            'create' => Pages\CreateFilm::route('/create'),
            'view' => Pages\ViewFilm::route('/{record}'),
            'edit' => Pages\EditFilm::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Normalisasi input sebelum disimpan (untuk form create & edit).
     */
    public static function sanitizeFormData(array $data): array
    {
        foreach (['judul', 'genre'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim(strip_tags($data[$field]));
            }
        }

        if (isset($data['sinopsis']) && is_string($data['sinopsis'])) {
            $data['sinopsis'] = trim(strip_tags($data['sinopsis']));
        }

        if (array_key_exists('rating', $data) && $data['rating'] === '') {
            $data['rating'] = null;
        }

        if (array_key_exists('tanggal_rilis', $data) && $data['tanggal_rilis'] === '') {
            $data['tanggal_rilis'] = null;
        }

        return $data;
    }
}
