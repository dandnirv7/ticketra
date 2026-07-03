<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SnackOrderResource\Pages;
use App\Models\SnackOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SnackOrderResource extends Resource
{
    protected static ?string $model = SnackOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Snack Orders';

    protected static ?string $modelLabel = 'Pesanan Snack';

    protected static ?string $pluralModelLabel = 'Pesanan Snack';

    protected static ?string $navigationGroup = 'Manajemen F&B';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'order_id';

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
                Forms\Components\Section::make('Informasi Pesanan Snack')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status Pesanan F&B')
                            ->required()
                            ->options([
                                'draft' => 'Draft (Belum Bayar)',
                                'locked' => 'Terkunci (Menunggu Konfirmasi)',
                                'pending' => 'Pending',
                                'paid' => 'Paid / Disiapkan Dapur',
                                'ready' => 'Siap Diambil',
                                'completed' => 'Selesai / Diambil',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->helperText('Ubah status pesanan untuk alur dapur/pengambilan F&B.'),

                        Forms\Components\Select::make('user_id')
                            ->label('Pelanggan')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('booking_id')
                            ->label('Terhubung dengan Booking Tiket')
                            ->relationship('booking', 'booking_id')
                            ->disabled()
                            ->placeholder('Stand-alone Snack Order')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('fnb_total')
                            ->label('Total F&B')
                            ->disabled()
                            ->prefix('Rp')
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Waktu Pembayaran')
                            ->disabled()
                            ->native(false)
                            ->displayFormat('d M Y H:i'),
                    ]),

                Forms\Components\Section::make('Item F&B Dipesan')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->disabled()
                            ->schema([
                                Forms\Components\TextInput::make('snack_emoji')
                                    ->label('Emoji')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('snack_name')
                                    ->label('Nama Snack')
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('qty')
                                    ->label('Jumlah')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Satuan')
                                    ->prefix('Rp')
                                    ->columnSpan(2),
                            ])
                            ->columns(7)
                            ->deletable(false)
                            ->addable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->striped()
            ->deferLoading()
            ->emptyStateHeading('Belum ada pesanan snack')
            ->emptyStateDescription('Pesanan F&B akan muncul di sini saat pelanggan memesan snack.')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.booking_id')
                    ->label('Tiket ID')
                    ->searchable()
                    ->placeholder('Stand-alone F&B'),

                Tables\Columns\TextColumn::make('fnb_total')
                    ->label('Total F&B')
                    ->sortable()
                    ->money('IDR')
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'locked' => 'warning',
                        'paid' => 'warning',
                        'ready' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'locked' => 'Terkunci',
                        'paid' => 'Dibayar (Dapur)',
                        'ready' => 'Siap Diambil',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                        'pending' => 'Pending',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Pesan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status F&B')
                    ->options([
                        'draft' => 'Draft',
                        'locked' => 'Terkunci',
                        'pending' => 'Pending',
                        'paid' => 'Dibayar (Dapur)',
                        'ready' => 'Siap Diambil',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Kelola Status'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record)),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportCsv')
                    ->label('Export CSV Laporan')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn() => route('admin.export.snacks'))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => static::userHasAnyRole(['admin'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSnackOrders::route('/'),
            'edit' => Pages\EditSnackOrder::route('/{record}/edit'),
        ];
    }
}
