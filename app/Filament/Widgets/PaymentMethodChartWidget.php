<?php

namespace App\Filament\Widgets;

use App\Models\PaymentWebhook;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaymentMethodChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Metode Pembayaran';

    protected static ?string $description = '30 hari terakhir (berdasarkan webhook Midtrans)';

    protected static ?string $maxHeight = '280px';

    protected static ?int $sort = 5;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $start = Carbon::now()->subDays(30)->startOfDay();

        $rows = PaymentWebhook::query()
            ->selectRaw('payment_type, COUNT(*) as total')
            ->where('processed_at', '>=', $start)
            ->whereNotNull('payment_type')
            ->groupBy('payment_type')
            ->orderByDesc('total')
            ->get();

        $labels = $rows->pluck('payment_type')->map(fn ($v) => $this->label($v))->all();
        $values = $rows->pluck('total')->map(fn ($v) => (int) $v)->all();
        $colors = $rows->pluck('payment_type')->map(fn ($v) => $this->color($v))->all();

        return [
            'datasets' => [
                [
                    'label' => 'Metode Pembayaran',
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => ['boxWidth' => 12, 'padding' => 10],
                ],
            ],
            'cutout' => '55%',
        ];
    }

    private function label(string $type): string
    {
        return match ($type) {
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Transfer Bank',
            'echannel' => 'Mandiri e-Channel',
            'bca_va', 'bni_va', 'bri_va', 'cimb_va', 'permata_va', 'other_va' => 'Virtual Account',
            'gopay', 'shopeepay', 'dana', 'ovo' => 'E-Wallet',
            'qris' => 'QRIS',
            'cstore' => 'Convenience Store',
            'akulaku' => 'Akulaku',
            default => ucwords(str_replace('_', ' ', $type)),
        };
    }

    private function color(string $type): string
    {
        return match (true) {
            str_ends_with($type, '_va') || $type === 'echannel' => 'rgb(59, 130, 246)',
            in_array($type, ['gopay', 'shopeepay', 'dana', 'ovo'], true) => 'rgb(16, 185, 129)',
            $type === 'qris' => 'rgb(239, 68, 68)',
            $type === 'credit_card' => 'rgb(245, 158, 11)',
            $type === 'cstore' => 'rgb(139, 92, 246)',
            default => 'rgb(107, 114, 128)',
        };
    }
}
