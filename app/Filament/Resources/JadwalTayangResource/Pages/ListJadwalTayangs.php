<?php

namespace App\Filament\Resources\JadwalTayangResource\Pages;

use App\Filament\Resources\JadwalTayangResource;
use App\Models\Studio;
use App\Services\BulkJadwalConflictException;
use App\Services\BulkJadwalGenerator;
use App\Services\BulkJadwalInput;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListJadwalTayangs extends ListRecords
{
    protected static string $resource = JadwalTayangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulkGenerate')
                ->label('Bulk Generate')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->modalHeading('Bulk Generate Jadwal Tayang')
                ->modalDescription('Generate banyak jadwal tayang sekaligus dengan aturan pricing weekday/weekend.')
                ->modalSubmitActionLabel('Generate')
                ->modalWidth('4xl')
                ->form($this->getBulkGenerateFormSchema())
                ->action(function (array $data) {
                    $this->runBulkGenerate($data);
                }),

            Actions\CreateAction::make()
                ->label('Tambah Manual'),
        ];
    }

    
    private function getBulkGenerateFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Pilih Film & Studio')
                ->description('Tentukan film yang akan ditayangkan dan studio-studio yang tersedia.')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('film_id')
                        ->label('Film')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => \App\Models\Film::query()
                            ->orderBy('judul')
                            ->get()
                            ->mapWithKeys(fn($f) => [$f->id => $f->judul]))
                        ->helperText('Pilih film yang akan di-generate jadwalnya.'),

                    Forms\Components\Select::make('studio_ids')
                        ->label('Studio')
                        ->required()
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->minItems(1)
                        ->options(fn() => Studio::query()
                            ->with('bioskop')
                            ->orderBy('nama')
                            ->get()
                            ->mapWithKeys(fn($s) => [
                                $s->id => $s->nama . ' — ' . ($s->bioskop?->nama ?? '-'),
                            ]))
                        ->helperText('Pilih 1 atau lebih studio. Tahan Ctrl/Cmd untuk pilih banyak.'),
                ]),

            Forms\Components\Section::make('Rentang Waktu')
                ->description('Tentukan kapan jadwal tayang akan di-generate.')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->native(false)
                        ->displayFormat('d F Y')
                        ->minDate(now()->startOfDay())
                        ->default(now()->addDay()->startOfDay()),

                    Forms\Components\DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->native(false)
                        ->displayFormat('d F Y')
                        ->minDate(now()->startOfDay())
                        ->afterOrEqual('tanggal_mulai')
                        ->default(now()->addDays(7)->startOfDay()),

                    Forms\Components\CheckboxList::make('hari_aktif')
                        ->label('Hari Aktif')
                        ->required()
                        ->minItems(1)
                        ->columns(7)
                        ->options([
                            1 => 'Sen',
                            2 => 'Sel',
                            3 => 'Rab',
                            4 => 'Kam',
                            5 => 'Jum',
                            6 => 'Sab',
                            7 => 'Min',
                        ])
                        ->default([1, 2, 3, 4, 5, 6, 7])
                        ->helperText('Centang hari-hari yang aktif.'),

                    Forms\Components\Repeater::make('skip_dates')
                        ->label('Skip Tanggal')
                        ->helperText('Tanggal yang akan di-skip (opsional).')
                        ->schema([
                            Forms\Components\DatePicker::make('date')
                                ->label('Tanggal')
                                ->required()
                                ->native(false)
                                ->displayFormat('d F Y'),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Tambah tanggal')
                        ->collapsed()
                        ->itemLabel(fn(array $state): ?string =>
                            $state['date'] ?? null
                        )
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('buffer_menit')
                        ->label('Buffer Antar Show')
                        ->required()
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->maxValue(120)
                        ->default(30)
                        ->suffix('menit')
                        ->helperText('Waktu tambahan untuk cleaning.')
                        ->columnSpan(2),
                ]),

            Forms\Components\Section::make('Jam Tayang & Harga')
                ->description('Tentukan jam tayang per hari dan harga per studio. Harga weekend otomatis ditambahkan dengan markup yang bisa diatur.')
                ->schema([
                    Forms\Components\Repeater::make('jam_tayang')
                        ->label('Jam Tayang')
                        ->required()
                        ->minItems(1)
                        ->schema([
                            Forms\Components\TimePicker::make('time')
                                ->label('Jam')
                                ->required()
                                ->native(false)
                                ->displayFormat('H:i')
                                ->minutesStep(5)
                                ->seconds(false),
                        ])
                        ->defaultItems(3)
                        ->addActionLabel('Tambah jam')
                        ->reorderable(false)
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('weekend_markup_rp')
                                ->label('Markup Weekend (Rp)')
                                ->helperText('Jumlah Rp yang ditambahkan ke harga weekday untuk weekend (Jumat-Minggu).')
                                ->required()
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->maxValue(100000)
                                ->default(10000)
                                ->prefix('+ Rp')
                                ->suffix('per tiket'),

                            Forms\Components\TextInput::make('holiday_markup_rp')
                                ->label('Markup Hari Libur (Rp)')
                                ->helperText('Jumlah Rp untuk tanggal yang ditandai libur (opsional).')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->maxValue(100000)
                                ->prefix('+ Rp')
                                ->suffix('per tiket')
                                ->placeholder('Kosongkan jika sama dengan weekend'),
                        ])
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('pricing')
                        ->label('Harga Weekday per Studio')
                        ->required()
                        ->minItems(1)
                        ->schema([
                            Forms\Components\Select::make('studio_id')
                                ->label('Studio')
                                ->required()
                                ->searchable()
                                ->options(fn() => Studio::query()
                                    ->with('bioskop')
                                    ->orderBy('nama')
                                    ->get()
                                    ->mapWithKeys(fn($s) => [
                                        $s->id => $s->nama . ' — ' . ($s->bioskop?->nama ?? '-'),
                                    ])),

                            Forms\Components\TextInput::make('default')
                                ->label('Harga Weekday')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->prefix('Rp')
                                ->helperText('Wajib > 0. Harga weekend = weekday + markup di atas.'),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Tambah studio')
                        ->reorderable(false)
                        ->collapsed()
                        ->itemLabel(fn(array $state): ?string =>
                            $state['studio_id']
                                ? Studio::find($state['studio_id'])?->nama
                                : '—'
                        )
                        ->columnSpanFull(),
                ]),
        ];
    }

    
    private function runBulkGenerate(array $data): void
    {
        try {
            $input = $this->buildServiceInput($data);
        } catch (\InvalidArgumentException $e) {
            Notification::make()
                ->title('Input tidak valid')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return;
        }

        try {
            $result = app(BulkJadwalGenerator::class)->generate($input);

            Notification::make()
                ->title('Berhasil generate jadwal')
                ->body("{$result->createdJadwals->count()} jadwal berhasil dibuat untuk film \"{$result->summary['film']}\" ({$result->summary['date_range']}).")
                ->success()
                ->send();

            
            $this->resetTable();
        } catch (BulkJadwalConflictException $e) {
            $conflictList = collect($e->conflicts)
                ->take(10) 
                ->map(fn($c) => "• {$c['studio_nama']} @ {$c['waktu_mulai']} (existing: {$c['existing_film']})")
                ->implode("\n");

            $moreCount = max(0, count($e->conflicts) - 10);
            $suffix = $moreCount > 0 ? "\n...dan {$moreCount} konflik lainnya." : '';

            Notification::make()
                ->title("Konflik: {$e->getMessage()}")
                ->body("Detail konflik:\n{$conflictList}{$suffix}")
                ->danger()
                ->persistent()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Generate gagal')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    private function buildServiceInput(array $form): BulkJadwalInput
    {
        $weekendMarkup = (float) ($form['weekend_markup_rp'] ?? 0);
        $holidayMarkup = isset($form['holiday_markup_rp']) && $form['holiday_markup_rp'] !== '' && $form['holiday_markup_rp'] !== null
            ? (float) $form['holiday_markup_rp']
            : null; 

        $pricing = [];
        foreach ($form['pricing'] ?? [] as $row) {
            $studioId = $row['studio_id'] ?? null;
            if (! $studioId) {
                throw new \InvalidArgumentException(
                    'Pilih studio dulu di setiap baris "Harga Weekday per Studio".'
                );
            }
            if (isset($pricing[$studioId])) {
                throw new \InvalidArgumentException(
                    'Studio yang sama dipilih lebih dari sekali di pricing.'
                );
            }

            $weekday = (float) ($row['default'] ?? 0);

            
            $pricing[$studioId] = [
                'default' => $weekday,
                'weekend' => $weekday + $weekendMarkup,    
                'holiday' => $weekday + ($holidayMarkup ?? $weekendMarkup),
            ];
        }

        $skipDates = collect($form['skip_dates'] ?? [])
            ->pluck('date')
            ->filter()
            ->values()
            ->all();

        $jamTayang = collect($form['jam_tayang'] ?? [])
            ->pluck('time')
            ->filter()
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->values()
            ->all();

        return new BulkJadwalInput(
            filmId: $form['film_id'],
            studioIds: $form['studio_ids'],
            tanggalMulai: Carbon::parse($form['tanggal_mulai'])->startOfDay(),
            tanggalSelesai: Carbon::parse($form['tanggal_selesai'])->endOfDay(),
            hariAktif: $form['hari_aktif'],
            jamTayang: $jamTayang,
            pricing: $pricing,
            bufferMenit: (int) ($form['buffer_menit'] ?? 30),
            skipDates: $skipDates,
        );
    }
}
