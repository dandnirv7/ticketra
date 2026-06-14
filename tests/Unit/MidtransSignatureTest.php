<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MidtransSignatureTest extends TestCase
{
    public function test_signature_matches_official_formula(): void
    {
        $orderId = 'TKT-1700000000-ABCDE';
        $statusCode = '200';
        $grossAmount = '100000.00';
        $serverKey = 'SB-Mid-server-XXXXXXXXXXXXXXXXXXXXX';

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $this->assertSame(128, strlen($expected), 'SHA-512 hex digest must be 128 chars');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{128}$/', $expected);

        $this->assertSame($expected, hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey));
    }

    public function test_signature_changes_when_any_input_changes(): void
    {
        $base = [
            'order_id' => 'TKT-1',
            'status_code' => '200',
            'gross_amount' => '100000',
            'server_key' => 'k',
        ];

        $signature = fn(array $p) => hash('sha512', $p['order_id'] . $p['status_code'] . $p['gross_amount'] . $p['server_key']);

        $baseSig = $signature($base);

        $this->assertNotSame($baseSig, $signature(array_merge($base, ['order_id' => 'TKT-2'])));
        $this->assertNotSame($baseSig, $signature(array_merge($base, ['status_code' => '201'])));
        $this->assertNotSame($baseSig, $signature(array_merge($base, ['gross_amount' => '100001'])));
        $this->assertNotSame($baseSig, $signature(array_merge($base, ['server_key' => 'k2'])));
    }

    public function test_signature_is_concatenation_in_exact_order(): void
    {
        $orderId = 'A';
        $statusCode = 'B';
        $grossAmount = 'C';
        $serverKey = 'D';

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $reordered = hash('sha512', $serverKey . $grossAmount . $statusCode . $orderId);

        $this->assertNotSame($expected, $reordered);
    }
}
