<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Promo & Kupon';

    protected static ?string $modelLabel = 'Promo';

    protected static ?string $pluralModelLabel = 'Promo & Kupon';

    protected static ?string $navigationGroup = 'Pemasaran';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'code';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Promo / Kupon')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Promo')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('MISAL: POPCORNGRATIS')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Promo')
                            ->required()
                            ->placeholder('Promo Popcorn Hemat')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('emoji')
                            ->label('Emoji')
                            ->default('🍿')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Potongan')
                            ->required()
                            ->options([
                                'fixed' => 'Potongan Tetap (Rp)',
                                'percentage' => 'Persentase (%)',
                            ])
                            ->default('fixed'),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Nilai Potongan')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Jika persentase, isi misal 20 untuk 20%'),

                        Forms\Components\TextInput::make('min_purchase')
                            ->label('Minimal Transaksi (Rp)')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp'),

                        Forms\Components\TextInput::make('max_uses')
                            ->label('Batas Maksimal Penggunaan')
                            ->numeric()
                            ->nullable()
                            ->helperText('Kosongkan jika tidak berbatas'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Waktu Mulai')
                            ->native(false)
                            ->displayFormat('d M Y H:i'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Waktu Berakhir')
                            ->native(false)
                            ->displayFormat('d M Y H:i'),

                        Forms\Components\Textarea::make('desc')
                            ->label('Deskripsi Promo')
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('emoji')
                    ->label('Icon'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Promo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => $state === 'percentage' ? 'Persentase' : 'Flat Rp'),

                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Potongan')
                    ->formatStateUsing(fn($record) => $record->type === 'percentage' ? $record->discount_amount . '%' : 'Rp ' . number_format($record->discount_amount, 0, ',', '.')),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('Terpakai')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::userHasAnyRole(['admin'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }
}
