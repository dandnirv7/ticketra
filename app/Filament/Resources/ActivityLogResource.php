<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Log Aktivitas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('System'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn(string $state): string => class_basename($state))
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('event')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'created' => 'Buat',
                        'updated' => 'Ubah',
                        'deleted' => 'Hapus',
                        'restored' => 'Pulihkan',
                        default => $state ?? '-',
                    }),
                Tables\Columns\ViewColumn::make('properties')
                    ->label('Detail')
                    ->view('filament.columns.activity-properties'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Aksi')
                    ->options([
                        'created' => 'Buat',
                        'updated' => 'Ubah',
                        'deleted' => 'Hapus',
                        'restored' => 'Pulihkan',
                    ]),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Model')
                    ->options(function () {
                        return Activity::query()
                            ->select('subject_type')
                            ->distinct()
                            ->whereNotNull('subject_type')
                            ->pluck('subject_type')
                            ->mapWithKeys(fn($type) => [$type => class_basename($type)])
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detail Log')
                    ->modalContent(function (Activity $record) {
                        $old = $record->properties['old'] ?? [];
                        $new = $record->properties['attributes'] ?? [];

                        return view('filament.modals.activity-detail', [
                            'record' => $record,
                            'old' => $old,
                            'new' => $new,
                        ]);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
