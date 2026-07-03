<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefundRequestResource\Pages;
use App\Models\RefundRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RefundRequestResource extends Resource
{
    protected static ?string $model = RefundRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Refund';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Refund')
                    ->schema([
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')
                            ->relationship('booking', 'booking_id')
                            ->searchable()
                            ->required()
                            ->disabled(fn($operation) => $operation === 'edit'),
                        Forms\Components\Select::make('user_id')
                            ->label('Pelanggan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(fn($operation) => $operation === 'edit'),
                        Forms\Components\TextInput::make('reason')
                            ->label('Alasan Pembatalan')
                            ->required()
                            ->maxLength(500),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Refund')
                    ->schema([
                        Forms\Components\Select::make('refund_type')
                            ->label('Tipe Refund')
                            ->options([
                                'full' => 'Full Refund',
                                'partial' => 'Partial Refund',
                            ])
                            ->required()
                            ->default('full')
                            ->live(),
                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Jumlah Refund (Rp)')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->helperText('Total yang akan dikembalikan ke pelanggan.'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending (Menunggu)',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'refunded' => 'Telah Direfund',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Admin')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Diproses Pada'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking.booking_id')
                    ->label('Booking')
                    ->searchable()
                    ->sortable()
                    ->color(fn($record) => $record->booking?->status === 'cancelled' ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('refund_amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('refund_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'full' => 'danger',
                        'partial' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'full' => 'Full',
                        'partial' => 'Partial',
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        'refunded' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'refunded' => 'Refunded',
                    }),
                Tables\Columns\TextColumn::make('processor.name')
                    ->label('Diproses Oleh')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Tgl Proses')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('refund_type')
                    ->label('Tipe')
                    ->options([
                        'full' => 'Full',
                        'partial' => 'Partial',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Detail'),
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Refund')
                    ->modalDescription('Setujui permintaan refund dan lanjutkan proses pengembalian dana.')
                    ->action(function (RefundRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                        ]);
                        Notification::make()->success()->title('Refund disetujui')->send();
                    })
                    ->visible(fn(RefundRequest $record): bool => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (RefundRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                            'admin_notes' => $data['admin_notes'],
                        ]);
                        Notification::make()->danger()->title('Refund ditolak')->send();
                    })
                    ->visible(fn(RefundRequest $record): bool => $record->status === 'pending'),
                Tables\Actions\Action::make('mark_refunded')
                    ->label('Refund Selesai')
                    ->icon('heroicon-o-banknotes')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Refund Selesai')
                    ->modalDescription('Konfirmasi bahwa dana sudah dikembalikan ke pelanggan.')
                    ->action(function (RefundRequest $record) {
                        $record->update([
                            'status' => 'refunded',
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                        ]);
                        Notification::make()->success()->title('Refund ditandai selesai')->send();
                    })
                    ->visible(fn(RefundRequest $record): bool => $record->status === 'approved'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canDelete($record): bool
    {
        return false; // never delete refund records
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefundRequests::route('/'),
            'create' => Pages\CreateRefundRequest::route('/create'),
            'edit' => Pages\EditRefundRequest::route('/{record}/edit'),
        ];
    }
}
