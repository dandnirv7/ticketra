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
        $attentionCount = Booking::query()
            ->where(function (Builder $q) {
                $q->where('status', 'pending_payment')
                    ->orWhere('status', 'cancelled')
                    ->orWhere('status', 'failed')
                    ->orWhere(function (Builder $q2) {
                        $q2->where('status', 'locked')
                            ->where('lock_expiry', '<', now());
                    });
            })
            ->count();

        return $table
            ->query(
                Booking::query()
                    ->with(['user', 'jadwalTayang.film', 'jadwalTayang.studio'])
                    ->where(function (Builder $q) {
                        $q->where('status', 'pending_payment')
                            ->orWhere('status', 'cancelled')
                            ->orWhere('status', 'failed')
                            ->orWhere(function (Builder $q2) {
                                $q2->where('status', 'locked')
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
                        'pending_payment' => 'warning',
                        'locked' => 'info',
                        'cancelled', 'failed' => 'danger',
                        'confirmed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('lock_expiry')
                    ->label('Lock Expired')
                    ->since()
                    ->placeholder('—'),
            ])
            ->heading(new \Illuminate\Support\HtmlString('Booking Butuh Perhatian <span class="ml-1.5 px-2 py-0.5 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-full inline-flex items-center justify-center min-w-[20px] h-[20px] align-middle">' . $attentionCount . '</span>'))
            ->headerActions([
                Tables\Actions\Action::make('view_all')
                    ->label('Lihat semua')
                    ->icon('heroicon-m-arrow-right')
                    ->iconPosition(\Filament\Support\Enums\IconPosition::After)
                    ->url(fn() => BookingResource::getUrl('index'))
                    ->color('gray')
                    ->extraAttributes(['class' => 'font-semibold text-sm hover:underline']),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Detail')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('warning')
                    ->url(fn (Booking $record) => BookingResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Tidak ada booking yang perlu perhatian')
            ->emptyStateDescription('Semua booking dalam status normal.');
    }
}
