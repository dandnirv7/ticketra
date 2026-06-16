<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Models\PaymentWebhook;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Aktivitas Terbaru';

    protected static ?string $description = 'Booking dan webhook Midtrans 24 jam terakhir';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['user', 'jadwalTayang.film', 'paymentWebhooks' => function ($q) {
                        $q->latest('processed_at')->limit(1);
                    }])
                    ->where('created_at', '>=', now()->subDay())
                    ->latest('created_at')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_id')
                    ->label('ID Booking')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemesan')
                    ->default('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jadwalTayang.film.judul')
                    ->label('Film')
                    ->limit(25)
                    ->wrap(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Booking')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancel', 'expired' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('latest_payment_status')
                    ->label('Pembayaran')
                    ->state(function (Booking $record) {
                        $latest = $record->paymentWebhooks->first();
                        return $latest?->status ?? '—';
                    })
                    ->badge()
                    ->color(function ($state) {
                        if (! is_string($state)) {
                            return 'gray';
                        }
                        return match ($state) {
                            'settlement', 'capture' => 'success',
                            'pending' => 'warning',
                            'deny', 'cancel', 'expire', 'failure' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->placeholder('—'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Detail')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Booking $record) => BookingResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('Belum ada aktivitas 24 jam terakhir');
    }
}
