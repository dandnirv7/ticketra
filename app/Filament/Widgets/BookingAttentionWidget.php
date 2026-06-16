<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class BookingAttentionWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Booking Butuh Perhatian';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['user', 'jadwalTayang.film', 'jadwalTayang.studio'])
                    ->where(function (Builder $q) {
                        $q->where('status', 'pending')
                            ->orWhere('status', 'cancel')
                            ->orWhere(function (Builder $q2) {
                                $q2->where('status', 'pending')
                                    ->where('lock_expiry', '<', now());
                            });
                    })
                    ->latest('updated_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_id')
                    ->label('ID Booking')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemesan')
                    ->default('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jadwalTayang.film.judul')
                    ->label('Film')
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('jadwalTayang.waktu_mulai')
                    ->label('Jadwal')
                    ->dateTime('d M Y, H:i'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'cancel', 'expired' => 'danger',
                        'paid' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('lock_expiry')
                    ->label('Lock Expired')
                    ->since()
                    ->placeholder('—'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Detail')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Booking $record) => BookingResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Tidak ada booking yang perlu perhatian')
            ->emptyStateDescription('Semua booking dalam status normal.');
    }
}
