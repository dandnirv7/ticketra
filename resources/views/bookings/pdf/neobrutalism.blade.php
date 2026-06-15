<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>E-Ticket {{ $booking->booking_id }}</title>
  <style>
    @page {
      margin: 0;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Helvetica', 'Arial', sans-serif;
      background: #fef9f0;
      color: #1f1d2c;
    }

    .page {
      padding: 36px 28px;
    }

    .pass {
      max-width: 820px;
      margin: 0 auto;
      background: #ffffff;
      border: 3px solid #1f1d2c;
      border-radius: 22px;
      overflow: hidden;
      position: relative;
    }

    .blob {
      position: absolute;
      border-radius: 50%;
      opacity: .55;
      z-index: 0;
    }

    .blob-1 {
      width: 90px;
      height: 90px;
      background: #ffd6e8;
      top: -20px;
      right: -20px;
    }

    .blob-2 {
      width: 60px;
      height: 60px;
      background: #c7f0db;
      bottom: 30px;
      left: -15px;
    }

    .blob-3 {
      width: 40px;
      height: 40px;
      background: #ffe7a3;
      top: 60%;
      right: 18px;
    }

    .head {
      background: #ffd6e8;
      border-bottom: 3px solid #1f1d2c;
      padding: 16px 24px;
      position: relative;
      z-index: 1;
    }

    .head-inner {
      display: table;
      width: 100%;
    }

    .brand-cell {
      display: table-cell;
      vertical-align: middle;
    }

    .brand {
      font-size: 12px;
      font-weight: 900;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #1f1d2c;
    }

    .brand .pill {
      display: inline-block;
      background: #1f1d2c;
      color: #ffd6e8;
      padding: 4px 10px;
      border-radius: 999px;
      margin-right: 8px;
      font-size: 10px;
      letter-spacing: 1.5px;
    }

    .id-cell {
      display: table-cell;
      vertical-align: middle;
      text-align: right;
    }

    .id-tag {
      display: inline-block;
      background: #ffffff;
      border: 2.5px solid #1f1d2c;
      border-radius: 999px;
      padding: 6px 14px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 1px;
      color: #1f1d2c;
    }

    .body {
      display: table;
      width: 100%;
      padding: 28px 24px 20px;
      position: relative;
      z-index: 1;
    }

    .poster-cell {
      display: table-cell;
      vertical-align: top;
      width: 188px;
      padding-right: 22px;
    }

    .info-cell {
      display: table-cell;
      vertical-align: top;
    }

    .poster {
      width: 188px;
      height: 250px;
      border: 3px solid #1f1d2c;
      border-radius: 14px;
      overflow: hidden;
      background: #c7f0db;
      position: relative;
    }

    .poster img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .poster-fallback {
      width: 100%;
      height: 100%;
      display: table;
      color: #1f1d2c;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .poster-fallback span {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
    }

    .poster-tag {
      position: absolute;
      top: 10px;
      left: -8px;
      background: #ffe7a3;
      border: 2.5px solid #1f1d2c;
      border-radius: 8px;
      padding: 4px 10px;
      font-size: 9px;
      font-weight: 900;
      letter-spacing: 1.5px;
      color: #1f1d2c;
    }

    .eyebrow {
      display: inline-block;
      background: #c7f0db;
      border: 2.5px solid #1f1d2c;
      border-radius: 8px;
      padding: 4px 10px;
      font-size: 10px;
      font-weight: 900;
      letter-spacing: 1.5px;
      color: #1f1d2c;
      margin-bottom: 12px;
    }

    .title {
      font-size: 30px;
      line-height: 1.1;
      font-weight: 900;
      color: #1f1d2c;
      letter-spacing: -0.5px;
      margin-bottom: 8px;
    }

    .sub {
      font-size: 12px;
      color: #1f1d2c;
      opacity: .7;
      margin-bottom: 22px;
      font-weight: 600;
    }

    .chips {
      display: table;
      width: 100%;
    }

    .chip {
      display: table-cell;
      vertical-align: top;
      padding-right: 12px;
    }

    .chip:last-child {
      padding-right: 0;
    }

    .chip-box {
      background: #ffffff;
      border: 2.5px solid #1f1d2c;
      border-radius: 12px;
      padding: 10px 12px;
    }

    .chip-l {
      font-size: 8px;
      letter-spacing: 1.5px;
      font-weight: 900;
      text-transform: uppercase;
      color: #1f1d2c;
      opacity: .7;
      margin-bottom: 4px;
    }

    .chip-v {
      font-size: 14px;
      font-weight: 900;
      color: #1f1d2c;
      line-height: 1.2;
    }

    .chip-v-sub {
      font-size: 10px;
      font-weight: 600;
      color: #1f1d2c;
      opacity: .7;
      margin-top: 2px;
    }

    .chip-c1 .chip-box {
      background: #ffe7a3;
    }

    .chip-c2 .chip-box {
      background: #c7f0db;
    }

    .chip-c3 .chip-box {
      background: #d6dcff;
    }

    .chip-c4 .chip-box {
      background: #ffd6e8;
    }

    .perf-wrap {
      position: relative;
      height: 22px;
      background: #ffffff;
    }

    .perf-wrap::before,
    .perf-wrap::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 22px;
      height: 22px;
      background: #fef9f0;
      border: 3px solid #1f1d2c;
      border-radius: 50%;
      transform: translateY(-50%);
    }

    .perf-wrap::before {
      left: -11px;
    }

    .perf-wrap::after {
      right: -11px;
    }

    .perf-dash {
      position: absolute;
      top: 50%;
      left: 22px;
      right: 22px;
      border-top: 2.5px dashed #1f1d2c;
      transform: translateY(-50%);
    }

    .stub {
      background: #d6dcff;
      padding: 22px 24px 24px;
      position: relative;
      z-index: 1;
    }

    .stub-row {
      display: table;
      width: 100%;
    }

    .stub-l {
      display: table-cell;
      vertical-align: middle;
      width: 38%;
    }

    .stub-m {
      display: table-cell;
      vertical-align: middle;
      width: 30%;
      padding: 0 14px;
    }

    .stub-r {
      display: table-cell;
      vertical-align: middle;
      text-align: right;
      width: 32%;
    }

    .seat-cap {
      font-size: 9px;
      letter-spacing: 1.5px;
      font-weight: 900;
      color: #1f1d2c;
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .seat-list {
      display: inline-block;
      background: #ffffff;
      border: 2.5px solid #1f1d2c;
      border-radius: 999px;
      padding: 6px 14px;
      font-size: 14px;
      font-weight: 900;
      letter-spacing: 1.5px;
      color: #1f1d2c;
    }

    .seat-sub {
      font-size: 10px;
      color: #1f1d2c;
      opacity: .7;
      margin-top: 6px;
      font-weight: 700;
    }

    .order-card {
      display: inline-block;
      background: #ffe7a3;
      border: 2.5px solid #1f1d2c;
      border-radius: 12px;
      padding: 8px 12px;
      text-align: left;
    }

    .order-l {
      font-size: 8px;
      letter-spacing: 1.5px;
      font-weight: 900;
      color: #1f1d2c;
      text-transform: uppercase;
    }

    .order-v {
      font-size: 16px;
      font-weight: 900;
      color: #1f1d2c;
      letter-spacing: 1px;
    }

    .qr-wrap {
      display: inline-block;
      background: #ffffff;
      border: 3px solid #1f1d2c;
      border-radius: 14px;
      padding: 8px;
    }

    .qr-wrap img {
      width: 110px;
      height: 110px;
      display: block;
      image-rendering: pixelated;
      image-rendering: crisp-edges;
    }

    .qr-cap {
      text-align: center;
      font-size: 9px;
      letter-spacing: 1.5px;
      font-weight: 900;
      color: #1f1d2c;
      text-transform: uppercase;
      margin-top: 6px;
    }

    .barcode-row {
      display: table;
      width: 100%;
      margin-top: 18px;
      padding-top: 16px;
      border-top: 2.5px dashed #1f1d2c;
    }

    .bc-l {
      display: table-cell;
      vertical-align: middle;
    }

    .bc-r {
      display: table-cell;
      vertical-align: middle;
      text-align: right;
    }

    .bc-l .barcode-inner {
      display: inline-block;
      background: #ffffff;
      border: 2.5px solid #1f1d2c;
      border-radius: 8px;
      padding: 6px 10px;
    }

    .bc-l img {
      max-width: 280px;
      max-height: 70px;
      display: block;
      image-rendering: pixelated;
    }

    .bc-r .note {
      font-size: 9px;
      letter-spacing: 1.5px;
      font-weight: 900;
      color: #1f1d2c;
      text-transform: uppercase;
    }

    .meta {
      max-width: 820px;
      margin: 18px auto 0;
      text-align: center;
      font-size: 10px;
      font-weight: 700;
      color: #1f1d2c;
      opacity: .6;
    }
  </style>
</head>

<body>
  <div class="page">
    <div class="pass">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>

      <div class="head">
        <div class="head-inner">
          <div class="brand-cell">
            <div class="brand">
              <span class="pill">TICKETRA</span>
              Cinema Pass
            </div>
          </div>
          <div class="id-cell">
            <span class="id-tag">#{{ $booking->booking_id }}</span>
          </div>
        </div>
      </div>

      <div class="body">
        <div class="poster-cell">
          <div class="poster">
            <span class="poster-tag">E-TICKET</span>
            @if($booking->jadwalTayang->film->poster_url)
            <img src="{{ $booking->jadwalTayang->film->poster_url }}" alt="poster">
            @else
            <div class="poster-fallback"><span>No Poster</span></div>
            @endif
          </div>
        </div>
        <div class="info-cell">
          <div class="eyebrow">
            {{ strtoupper($booking->jadwalTayang->film->rating ?? 'SU') }}
            &middot; {{ $booking->jadwalTayang->film->genre ?? 'Cinema' }}
          </div>
          <div class="title">{{ $booking->jadwalTayang->film->judul }}</div>
          <div class="sub">
            {{ $booking->jadwalTayang->film->durasi ?? '—' }} min
            @if($booking->jadwalTayang->film->sutradara)
            &middot; Directed by {{ $booking->jadwalTayang->film->sutradara }}
            @endif
          </div>

          <div class="chips">
            <div class="chip chip-c1">
              <div class="chip-box">
                <div class="chip-l">Date</div>
                <div class="chip-v">{{ $booking->jadwalTayang->waktu_mulai->format('d M') }}</div>
                <div class="chip-v-sub">{{ $booking->jadwalTayang->waktu_mulai->translatedFormat('D, Y') }}</div>
              </div>
            </div>
            <div class="chip chip-c2">
              <div class="chip-box">
                <div class="chip-l">Time</div>
                <div class="chip-v">{{ $booking->jadwalTayang->waktu_mulai->format('H:i') }}</div>
                <div class="chip-v-sub">WIB</div>
              </div>
            </div>
            <div class="chip chip-c3">
              <div class="chip-box">
                <div class="chip-l">Hall</div>
                <div class="chip-v">{{ $booking->jadwalTayang->studio->nama }}</div>
                <div class="chip-v-sub">Studio</div>
              </div>
            </div>
            <div class="chip chip-c4">
              <div class="chip-box">
                <div class="chip-l">Cinema</div>
                <div class="chip-v" style="font-size:11px;line-height:1.2;">{{ $booking->jadwalTayang->studio->bioskop->nama }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="perf-wrap">
        <div class="perf-dash"></div>
      </div>

      <div class="stub">
        <div class="stub-row">
          <div class="stub-l">
            <div class="seat-cap">Seats</div>
            <div class="seat-list">{{ $seatsList }}</div>
            <div class="seat-sub">{{ $booking->statusKursis->count() }} tiket &middot; 1x scan</div>
          </div>
          <div class="stub-m">
            <div class="seat-cap" style="text-align:center;">Order</div>
            <div style="text-align:center;">
              <div class="order-card">
                <div class="order-l">No.</div>
                <div class="order-v">{{ $nomorOrder ?? $booking->booking_id }}</div>
              </div>
            </div>
          </div>
          <div class="stub-r">
            <div class="qr-wrap">
              <img src="data:image/png;base64,{{ $qrCode }}" alt="QR">
            </div>
            <div class="qr-cap">Scan at entry</div>
          </div>
        </div>

        <div class="barcode-row">
          <div class="bc-l">
            <div class="barcode-inner">
              <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
            </div>
          </div>
          <div class="bc-r">
            <div class="note">Valid 1x scan &middot; No reprint</div>
          </div>
        </div>
      </div>
    </div>

    <div class="meta">
      Dicetak otomatis {{ now()->format('d M Y, H:i') }} WIB &middot; Ticketra Cinema System
    </div>
  </div>
</body>

</html>