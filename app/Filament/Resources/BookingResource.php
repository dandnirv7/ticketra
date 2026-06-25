<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Film;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Booking';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'Booking';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'booking_id';

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
        return false;
    }

    public static function canEdit($record): bool
    {
        return static::userHasAnyRole(['admin']);
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
                Forms\Components\Section::make('Informasi Booking')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('booking_id')
                            ->label('Booking ID')
                            ->disabled()
                            ->columnSpan(2),

                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'locked' => 'Locked',
                                'pending_payment' => 'Pending Payment',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'failed' => 'Failed',
                            ])
                            ->helperText('Ubah status booking jika diperlukan.'),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Harga')
                            ->disabled()
                            ->prefix('Rp')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('service_fee')
                            ->label('Biaya Layanan')
                            ->disabled()
                            ->prefix('Rp'),

                        Forms\Components\TextInput::make('fnb_total')
                            ->label('F&B Total')
                            ->disabled()
                            ->prefix('Rp'),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Waktu Pembayaran')
                            ->disabled()
                            ->native(false)
                            ->displayFormat('d M Y H:i'),

                        Forms\Components\DateTimePicker::make('locked_at')
                            ->label('Dikunci')
                            ->disabled()
                            ->native(false)
                            ->displayFormat('d M Y H:i'),

                        Forms\Components\DateTimePicker::make('lock_expiry')
                            ->label('Lock Expiry')
                            ->disabled()
                            ->native(false)
                            ->displayFormat('d M Y H:i'),

                        Forms\Components\Select::make('jadwal_tayang_id')
                            ->label('Jadwal Tayang')
                            ->relationship('jadwalTayang', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->film?->judul . ' — ' . $record->studio?->nama . ' — ' . $record->waktu_mulai?->format('d M Y H:i'))
                            ->disabled()
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->striped()
            ->deferLoading()
            ->extremePaginationLinks()
            ->emptyStateHeading('Belum ada booking')
            ->emptyStateDescription('Booking akan muncul ketika user melakukan pemesanan tiket.')
            ->emptyStateIcon('heroicon-o-ticket')
            ->columns([
                Tables\Columns\TextColumn::make('booking_id')
                    ->label('Booking ID')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->tooltip(fn($record) => $record?->booking_id)
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jadwalTayang.film.judul')
                    ->label('Film')
                    ->searchable(query: fn(Builder $query, string $search) => $query->whereHas('jadwalTayang.film', fn($q) => $q->where('judul', 'like', '%' . $search . '%')))
                    ->limit(20)
                    ->tooltip(fn($record) => $record?->jadwalTayang?->film?->judul),

                Tables\Columns\TextColumn::make('jadwalTayang.waktu_mulai')
                    ->label('Tayang')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->sortable()
                    ->money('IDR')
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending_payment' => 'warning',
                        'locked' => 'info',
                        'cancelled' => 'danger',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending_payment' => 'Pending',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('statusKursis_count')
                    ->label('Kursi')
                    ->counts('statusKursis')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Sampah')
                    ->falseLabel('Aktif'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'locked' => 'Locked',
                        'pending_payment' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('film')
                    ->label('Film')
                    ->relationship('jadwalTayang.film', 'judul', fn(Builder $query) => $query->orderBy('judul'))
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record))
                    ->modalHeading('Hapus Booking')
                    ->modalDescription('Apakah Anda yakin ingin menghapus booking ini? Data dapat dipulihkan dari Sampah.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Booking berhasil dihapus.'),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn($record) => static::canRestore($record))
                    ->successNotificationTitle('Booking berhasil dipulihkan.'),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn($record) => static::canForceDelete($record))
                    ->modalHeading('Hapus Permanen')
                    ->modalDescription('Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus Permanen')
                    ->successNotificationTitle('Booking dihapus permanen.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::canDeleteAny())
                        ->modalHeading('Hapus Booking Terpilih')
                        ->modalDescription('Booking yang dipilih akan dipindahkan ke Sampah.')
                        ->successNotificationTitle('Booking berhasil dihapus.'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn() => static::canRestoreAny())
                        ->successNotificationTitle('Booking berhasil dipulihkan.'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn() => static::canForceDeleteAny())
                        ->modalHeading('Hapus Permanen Massal')
                        ->modalDescription('Tindakan ini tidak dapat dibatalkan.')
                        ->successNotificationTitle('Booking dihapus permanen.'),
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
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
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
