<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KursiResource\Pages;
use App\Models\Kursi;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;

class KursiResource extends Resource
{
    protected static ?string $model = Kursi::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Kursi';

    protected static ?string $modelLabel = 'Kursi';

    protected static ?string $pluralModelLabel = 'Kursi';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'label_baris';

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

    public static function form(Form $form): Form
    {
        $isCreatePage = request()->routeIs('filament.admin.resources.kursis.create');

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kursi')
                    ->description('Atur kursi di setiap studio.')
                    ->columns(2)
                    ->schema([
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
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_bulk')
                            ->label('Generate Massal (Bulk)')
                            ->default(false)
                            ->reactive()
                            ->visible(fn($livewire) => $livewire instanceof Pages\CreateKursi)
                            ->columnSpan(2)
                            ->helperText('Aktifkan untuk generate banyak kursi sekaligus.'),

                        Forms\Components\TextInput::make('label_baris')
                            ->label('Label Baris')
                            ->required(fn (Get $get) => !$get('is_bulk'))
                            ->visible(fn (Get $get) => !$get('is_bulk'))
                            ->maxLength(5)
                            ->regex('/^[A-Za-z0-9]+$/')
                            ->validationMessages([
                                'regex' => 'Label baris hanya boleh berisi huruf dan angka (tanpa spasi, koma, atau karakter khusus).',
                            ])
                            ->placeholder('Contoh: A'),

                        Forms\Components\TextInput::make('nomor_kursi')
                            ->label('Nomor Kursi')
                            ->required(fn (Get $get) => !$get('is_bulk'))
                            ->visible(fn (Get $get) => !$get('is_bulk'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(999)
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $studioId = $get('studio_id');
                                    $labelBaris = $get('label_baris');
                                    if (! $studioId || ! $labelBaris) {
                                        return;
                                    }

                                    $cleanLabelBaris = strtoupper(trim(strip_tags($labelBaris)));

                                    $query = \App\Models\Kursi::where('studio_id', $studioId)
                                        ->where('label_baris', $cleanLabelBaris)
                                        ->where('nomor_kursi', $value);

                                    $record = request()->route('record');
                                    if ($record) {
                                        $query->where('id', '!=', $record);
                                    }

                                    if ($query->exists()) {
                                        $fail('Kursi dengan Baris ' . $cleanLabelBaris . ' dan Nomor ' . $value . ' sudah terdaftar di studio terpilih.');
                                    }
                                },
                            ]),

                        Forms\Components\Select::make('baris_mulai')
                            ->label('Baris Mulai')
                            ->required(fn (Get $get) => $get('is_bulk'))
                            ->visible(fn (Get $get) => $get('is_bulk'))
                            ->options(array_combine(range('A', 'Z'), range('A', 'Z')))
                            ->default('A'),

                        Forms\Components\Select::make('baris_selesai')
                            ->label('Baris Selesai')
                            ->required(fn (Get $get) => $get('is_bulk'))
                            ->visible(fn (Get $get) => $get('is_bulk'))
                            ->options(array_combine(range('A', 'Z'), range('A', 'Z')))
                            ->default('H')
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $mulai = $get('baris_mulai');
                                    if ($mulai && ord($value) < ord($mulai)) {
                                        $fail('Baris selesai harus sama atau setelah Baris Mulai.');
                                    }
                                }
                            ]),

                        Forms\Components\TextInput::make('jumlah_kursi_per_baris')
                            ->label('Jumlah Kursi per Baris')
                            ->required(fn (Get $get) => $get('is_bulk'))
                            ->visible(fn (Get $get) => $get('is_bulk'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(10)
                            ->columnSpan(2),

                        Forms\Components\Select::make('tipe_kursi')
                            ->label('Tipe Kursi')
                            ->required()
                            ->options([
                                'reguler' => 'Reguler',
                                'premium' => 'Premium',
                                'sweetbox' => 'Sweetbox',
                                'couple' => 'Couple',
                            ])
                            ->default('reguler'),

                        Forms\Components\Toggle::make('is_aktif')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Nonaktifkan jika kursi tidak tersedia.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('studio_id', 'asc')
            ->defaultSort('label_baris', 'asc')
            ->defaultSort('nomor_kursi', 'asc')
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100, 200])
            ->striped()
            ->deferLoading()
            ->extremePaginationLinks()
            ->emptyStateHeading('Belum ada data kursi')
            ->emptyStateDescription('Tambahkan kursi dengan menekan tombol "Tambah Kursi" di atas.')
            ->emptyStateIcon('heroicon-o-squares-2x2')
            ->columns([
                Tables\Columns\TextColumn::make('studio.nama')
                    ->label('Studio')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record?->studio?->bioskop?->nama),

                Tables\Columns\TextColumn::make('label_baris')
                    ->label('Baris')
                    ->sortable()
                    ->alignment('center')
                    ->weight('medium')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nomor_kursi')
                    ->label('No. Kursi')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('tipe_kursi')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'premium' => 'warning',
                        'sweetbox' => 'pink',
                        'couple' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                Tables\Columns\IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignment('center'),

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
                Tables\Filters\SelectFilter::make('studio_id')
                    ->label('Studio')
                    ->options(fn() => Studio::query()
                        ->with('bioskop')
                        ->orderBy('nama')
                        ->get()
                        ->mapWithKeys(fn($s) => [$s->id => $s->nama . ' — ' . ($s->bioskop?->nama ?? '-')]))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('tipe_kursi')
                    ->label('Tipe')
                    ->options([
                        'reguler' => 'Reguler',
                        'premium' => 'Premium',
                        'sweetbox' => 'Sweetbox',
                        'couple' => 'Couple',
                    ]),
                Tables\Filters\Filter::make('is_aktif')
                    ->label('Hanya Aktif')
                    ->toggle()
                    ->query(fn(Builder $query) => $query->where('is_aktif', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record))
                    ->modalHeading('Hapus Kursi')
                    ->modalDescription('Apakah Anda yakin ingin menghapus kursi ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Kursi berhasil dihapus.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::canDeleteAny())
                        ->modalHeading('Hapus Kursi Terpilih')
                        ->modalDescription('Kursi yang dipilih akan dihapus.')
                        ->successNotificationTitle('Kursi berhasil dihapus.'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKursis::route('/'),
            'create' => Pages\CreateKursi::route('/create'),
            'view' => Pages\ViewKursi::route('/{record}'),
            'edit' => Pages\EditKursi::route('/{record}/edit'),
        ];
    }

    public static function sanitizeFormData(array $data): array
    {
        if (isset($data['label_baris']) && is_string($data['label_baris'])) {
            $data['label_baris'] = strtoupper(trim(strip_tags($data['label_baris'])));
        }

        return $data;
    }
}

