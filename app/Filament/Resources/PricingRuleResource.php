<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingRuleResource\Pages;
use App\Models\PricingRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PricingRuleResource extends Resource
{
    protected static ?string $model = PricingRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Aturan Pricing Tiket';

    protected static ?string $modelLabel = 'Aturan Pricing';

    protected static ?string $pluralModelLabel = 'Aturan Pricing Tiket';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

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
        return static::userHasAnyRole(['admin']);
    }

    public static function canEdit($record): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function canDelete($record): bool
    {
        return static::userHasAnyRole(['admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konfigurasi Dynamic Pricing')
                    ->description('Atur surcharge / kenaikan harga tiket otomatis (misal: Sabtu & Minggu surcharge Rp 10.000).')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Aturan')
                            ->required()
                            ->placeholder('Surcharge Weekend (+10rb)')
                            ->columnSpan(1),

                        Forms\Components\Select::make('day_type')
                            ->label('Berlaku Pada Hari')
                            ->required()
                            ->options([
                                'weekend' => 'Weekend (Sabtu & Minggu)',
                                'weekday' => 'Weekday (Senin - Jumat)',
                            ])
                            ->default('weekend'),

                        Forms\Components\Select::make('surcharge_type')
                            ->label('Jenis Biaya Tambahan')
                            ->required()
                            ->options([
                                'fixed' => 'Nominal Tetap (Rp)',
                                'percentage' => 'Persentase (%)',
                            ])
                            ->default('fixed'),

                        Forms\Components\TextInput::make('surcharge_amount')
                            ->label('Nilai Biaya Tambahan')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('10000')
                            ->helperText('Jika nominal tetap: isi 10000. Jika persentase: isi 15 untuk 15%.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Aturan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('day_type')
                    ->label('Tipe Hari')
                    ->badge()
                    ->color(fn(string $state) => $state === 'weekend' ? 'danger' : 'info')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'weekend' => 'Weekend (Sabtu-Minggu)',
                        'weekday' => 'Weekday (Senin-Jumat)',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('surcharge_amount')
                    ->label('Tambahan Biaya')
                    ->formatStateUsing(fn($record) => $record->surcharge_type === 'percentage' ? '+' . $record->surcharge_amount . '%' : '+Rp ' . number_format($record->surcharge_amount, 0, ',', '.')),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i')
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
            'index' => Pages\ListPricingRules::route('/'),
            'create' => Pages\CreatePricingRule::route('/create'),
            'edit' => Pages\EditPricingRule::route('/{record}/edit'),
        ];
    }
}
