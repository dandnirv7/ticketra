<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class CustomerOverview extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Data Pelanggan';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.customer-overview';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'editor']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->select([
                        'users.*',
                        \DB::raw('(SELECT COUNT(*) FROM bookings WHERE bookings.user_id = users.id) as total_bookings'),
                        \DB::raw('(SELECT COALESCE(SUM(bookings.total_price + bookings.service_fee + bookings.fnb_total - bookings.discount_amount), 0) FROM bookings WHERE bookings.user_id = users.id AND bookings.status = \'confirmed\') as total_spend'),
                        \DB::raw('(SELECT MAX(bookings.created_at) FROM bookings WHERE bookings.user_id = users.id) as last_booking_at'),
                        \DB::raw('(SELECT films.judul FROM bookings JOIN jadwal_tayangs ON bookings.jadwal_tayang_id = jadwal_tayangs.id JOIN films ON jadwal_tayangs.film_id = films.id WHERE bookings.user_id = users.id AND bookings.status = \'confirmed\' GROUP BY films.judul ORDER BY COUNT(*) DESC LIMIT 1) as favorite_film'),
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'danger',
                        'editor' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_bookings')
                    ->label('Total Booking')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_spend')
                    ->label('Total Spend')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('favorite_film')
                    ->label('Film Favorit')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('last_booking_at')
                    ->label('Booking Terakhir')
                    ->dateTime('d M Y')
                    ->placeholder('Belum pernah')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Daftar')
                    ->dateTime('d M Y'),
            ])
            ->defaultSort('total_spend', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'user' => 'User',
                        'editor' => 'Editor',
                        'admin' => 'Admin',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_bookings')
                    ->label('Lihat Booking')
                    ->icon('heroicon-o-ticket')
                    ->color('primary')
                    ->url(fn(User $record): string => "/admin/bookings?tableFilters[user_id][value]={$record->id}"),
            ])
            ->bulkActions([]);
    }
}
