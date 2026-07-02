<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎨 Palette Preview — Ticketra</title>
    @vite('resources/css/app.css')
    <style>
        :root {
            --border-w: 3px;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f5f0eb;
            padding: 2rem;
        }
        .page-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 3rem;
            letter-spacing: -0.03em;
        }
        .grid { display: flex; flex-wrap: wrap; gap: 2rem; justify-content: center; }

        /* Palette Card */
        .palette-card {
            width: 360px;
            background: #fff;
            border: var(--border-w) solid #111;
            border-radius: 16px;
            box-shadow: 8px 8px 0 #111;
            overflow: hidden;
        }
        .palette-header {
            padding: 1.2rem 1.5rem;
            border-bottom: var(--border-w) solid #111;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: -0.01em;
        }
        .palette-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }

        /* Swatch */
        .swatch-group { display: flex; flex-direction: column; gap: 0.4rem; }
        .swatch-row { display: flex; align-items: center; gap: 0.6rem; }
        .swatch {
            width: 36px; height: 36px;
            border: 2px solid #111;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .swatch-info { }
        .swatch-label { font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.02em; line-height: 1.2; }
        .swatch-value { font-size: 0.65rem; font-family: monospace; color: #555; line-height: 1.2; }

        /* Mini UI Preview */
        .ui-mini {
            margin-top: 0.5rem;
            padding: 1rem;
            border: 2px solid #111;
            border-radius: 10px;
            display: flex; flex-direction: column; gap: 0.5rem;
        }
        .ui-mini-header { font-weight: 800; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .ui-mini-row { display: flex; gap: 0.4rem; flex-wrap: wrap; }
        .ui-btn {
            padding: 0.35rem 0.8rem;
            border: 2px solid #111;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            cursor: default;
        }
        .ui-badge {
            padding: 0.15rem 0.5rem;
            border: 2px solid #111;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.55rem;
            text-transform: uppercase;
        }
        .ui-card {
            padding: 0.6rem;
            border: 2px solid #111;
            border-radius: 8px;
            font-size: 0.55rem;
            font-weight: 600;
        }

        .palette-footer {
            padding: 0.8rem 1.5rem;
            border-top: var(--border-w) solid #111;
            font-size: 0.7rem;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        @media (max-width: 768px) {
            body { padding: 1rem; }
            .palette-card { width: 100%; max-width: 360px; }
        }
    </style>
</head>
<body>

<h1 class="page-title">🎨 Color Palette Preview — 4 Opsi</h1>

<div class="grid">

{{-- ────────────────────────────── PALETTE 1 ────────────────────────────── --}}
@php
$p1 = [
    'name' => '🌿 Hijau-Coral Gold',
    'tag' => 'hangat · bioskop · earthy',
    'bg' => '#f5f0eb',
    'surface' => '#ffffff',
    'primary' => '#66bb6a',
    'secondary' => '#f9a8a8',
    'accent' => '#facc15',
    'foreground' => '#1a1a2e',
    'colors' => [
        ['label' => 'Background', 'val' => '#f5f0eb (cream)', 'bg' => '#f5f0eb'],
        ['label' => 'Surface',    'val' => '#ffffff',          'bg' => '#ffffff'],
        ['label' => 'Primary',    'val' => '#66bb6a (green)',  'bg' => '#66bb6a'],
        ['label' => 'Secondary',  'val' => '#f9a8a8 (coral)',  'bg' => '#f9a8a8'],
        ['label' => 'Accent',     'val' => '#facc15 (gold)',   'bg' => '#facc15'],
        ['label' => 'Foreground', 'val' => '#1a1a2e (navy)',   'bg' => '#1a1a2e'],
    ],
];
$p2 = [
    'name' => '🩷 Pink-Hijau Lemon',
    'tag' => 'playful · cinema · energetic',
    'bg' => '#e6f7f0',
    'surface' => '#ffffff',
    'primary' => '#f9a8a8',
    'secondary' => '#66bb6a',
    'accent' => '#fde047',
    'foreground' => '#1e1b4b',
    'colors' => [
        ['label' => 'Background', 'val' => '#e6f7f0 (mint)',   'bg' => '#e6f7f0'],
        ['label' => 'Surface',    'val' => '#ffffff',           'bg' => '#ffffff'],
        ['label' => 'Primary',    'val' => '#f9a8a8 (pink)',   'bg' => '#f9a8a8'],
        ['label' => 'Secondary',  'val' => '#66bb6a (green)',  'bg' => '#66bb6a'],
        ['label' => 'Accent',     'val' => '#fde047 (lemon)',  'bg' => '#fde047'],
        ['label' => 'Foreground', 'val' => '#1e1b4b (navy)',   'bg' => '#1e1b4b'],
    ],
];
$p3 = [
    'name' => '🟢 Hijau-Sky Lavender + Lemon',
    'tag' => 'branded · 4-color system',
    'bg' => '#eef6f3',
    'surface' => '#ffffff',
    'primary' => '#66bb6a',
    'brand'  => '#c4b5fd',
    'secondary' => '#93c5fd',
    'accent' => '#fde047',
    'foreground' => '#1e293b',
    'colors' => [
        ['label' => 'Background', 'val' => '#eef6f3 (mint)',     'bg' => '#eef6f3'],
        ['label' => 'Surface',    'val' => '#ffffff',             'bg' => '#ffffff'],
        ['label' => 'Primary 🟢',  'val' => '#66bb6a (green)',   'bg' => '#66bb6a'],
        ['label' => 'Brand 🟣',    'val' => '#c4b5fd (lavender)','bg' => '#c4b5fd'],
        ['label' => 'Secondary',  'val' => '#93c5fd (sky)',      'bg' => '#93c5fd'],
        ['label' => 'Accent 🟡',   'val' => '#fde047 (lemon)',   'bg' => '#fde047'],
        ['label' => 'Foreground', 'val' => '#1e293b (slate)',    'bg' => '#1e293b'],
    ],
];
$p4 = [
    'name' => '🟢 Monochrome Green',
    'tag' => 'minimal · one-brand · clean',
    'bg' => '#f0f7f0',
    'surface' => '#ffffff',
    'primary' => '#66bb6a',
    'secondary' => '#a8e6cf',
    'accent' => '#f9a8a8', /* coral variant */
    'foreground' => '#1f2937',
    'colors' => [
        ['label' => 'Background', 'val' => '#f0f7f0 (mint)',     'bg' => '#f0f7f0'],
        ['label' => 'Surface',    'val' => '#ffffff',             'bg' => '#ffffff'],
        ['label' => 'Primary',    'val' => '#66bb6a (green)',    'bg' => '#66bb6a'],
        ['label' => 'Secondary',  'val' => '#a8e6cf (mint)',     'bg' => '#a8e6cf'],
        ['label' => 'Accent',     'val' => '#f9a8a8 (coral) 🎯', 'bg' => '#f9a8a8'],
        ['label' => 'Foreground', 'val' => '#1f2937 (charcoal)', 'bg' => '#1f2937'],
    ],
];
$palettes = [$p1, $p2, $p3, $p4];
@endphp

@foreach ($palettes as $p)
<div class="palette-card" style="background: {{ $p['surface'] }};">
    <div class="palette-header" style="color: {{ $p['foreground'] }};">
        {{ $p['name'] }}
        <span style="font-weight:400;text-transform:none;font-size:0.7rem;display:block;color:#888;">
            {{ $p['tag'] }}
        </span>
    </div>

    <div class="palette-body">
        {{-- Color swatches --}}
        <div class="swatch-group">
            @foreach ($p['colors'] as $c)
            <div class="swatch-row">
                <div class="swatch" style="background:{{ $c['bg'] }}; @if($c['bg']==='#ffffff') outline:1px solid #ddd; @endif"></div>
                <div class="swatch-info">
                    <div class="swatch-label">{{ $c['label'] }}</div>
                    <div class="swatch-value">{{ $c['val'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Mini UI Preview --}}
        <div class="ui-mini" style="background:{{ $p['bg'] }};">
            @if(isset($p['brand']))
            <div class="ui-mini-row">
                <span class="ui-btn" style="background:{{ $p['brand'] }};color:{{ $p['foreground'] }};width:100%;text-align:center;">🎬 TICKETRA</span>
            </div>
            @endif
            <div class="ui-mini-header" style="color:{{ $p['foreground'] }};">Preview</div>

            <div class="ui-mini-row">
                <span class="ui-btn" style="background:{{ $p['primary'] }};color:{{ $p['surface'] }};">Beli Tiket</span>
                <span class="ui-btn" style="background:{{ $p['surface'] }};color:{{ $p['foreground'] }};">Lihat Jadwal</span>
            </div>

            <div class="ui-mini-row">
                <span class="ui-badge" style="background:{{ $p['accent'] }};color:{{ $p['foreground'] }};">Promo</span>
                <span class="ui-badge" style="background:{{ $p['secondary'] ?? $p['accent'] }};color:{{ $p['foreground'] }};">Romance</span>
                <span class="ui-badge" style="background:{{ $p['primary'] }};color:{{ $p['surface'] }};">Coming Soon</span>
            </div>

            <div class="ui-card" style="background:{{ $p['surface'] }};color:{{ $p['foreground'] }};border-color:{{ $p['primary'] }};">
                ⭐ 8.5 &nbsp;|&nbsp; Spiderman: Across the Spider-Verse
            </div>

            @if($loop->index === 2)
            <div class="ui-mini-header" style="color:{{ $p['foreground'] }};margin-top:0.25rem;">Opacity gradient → bg-primary/90 /50 /20</div>
            <div class="ui-mini-row" style="justify-content:center;">
                <span class="ui-btn" style="background:{{ $p['primary'] }};">100</span>
                <span class="ui-btn" style="background:{{ $p['primary'] }}70;">70</span>
                <span class="ui-btn" style="background:{{ $p['primary'] }}50;">50</span>
                <span class="ui-btn" style="background:{{ $p['primary'] }}30;">30</span>
                <span class="ui-btn" style="background:{{ $p['primary'] }}15;">15</span>
            </div>
            @endif
        </div>
    </div>

    <div class="palette-footer" style="background:{{ $p['primary'] }};color:{{ $p['surface'] }};">
        {{ $loop->index + 1 }} dari 4
    </div>
</div>
@endforeach

</div>

</body>
</html>
