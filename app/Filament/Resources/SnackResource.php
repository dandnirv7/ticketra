<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SnackResource\Pages;
use App\Models\Snack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SnackResource extends Resource
{
    protected static ?string $model = Snack::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Manajemen F&B';
    protected static ?string $navigationLabel = 'Katalog Snack & F&B';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi F&B')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID / SKU')
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Snack')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('emoji')
                            ->label('Emoji / Icon')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->required()
                            ->options([
                                'Popcorn' => 'Popcorn',
                                'Minuman' => 'Minuman',
                                'Snack' => 'Snack',
                                'Combo' => 'Combo',
                            ]),

                        Forms\Components\TextInput::make('price')
                            ->label('Harga (IDR)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),

                        Forms\Components\Select::make('status')
                            ->label('Status Availability')
                            ->options([
                                'TERSEDIA' => 'Tersedia',
                                'SISA SEDIKIT' => 'Sisa Sedikit',
                                'HABIS' => 'Habis',
                            ])
                            ->default('TERSEDIA')
                            ->required(),

                        Forms\Components\Select::make('bioskops')
                            ->label('Tersedia di Bioskop')
                            ->multiple()
                            ->relationship('bioskops', 'nama')
                            ->preload()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('desc')
                            ->label('Deskripsi')
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('SKU/ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('emoji')
                    ->label('Icon'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Item')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bioskops.nama')
                    ->label('Lokasi Bioskop')
                    ->badge()
                    ->separator(','),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TERSEDIA' => 'success',
                        'SISA SEDIKIT' => 'warning',
                        'HABIS' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bioskops')
                    ->relationship('bioskops', 'nama')
                    ->label('Filter Bioskop'),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Popcorn' => 'Popcorn',
                        'Minuman' => 'Minuman',
                        'Snack' => 'Snack',
                        'Combo' => 'Combo',
                    ])
                    ->label('Filter Kategori'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSnacks::route('/'),
            'create' => Pages\CreateSnack::route('/create'),
            'edit' => Pages\EditSnack::route('/{record}/edit'),
        ];
    }
}
