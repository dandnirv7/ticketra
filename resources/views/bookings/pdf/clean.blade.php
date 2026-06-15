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
      background: #f5f5f4;
      color: #1c1917;
      -webkit-font-smoothing: antialiased;
    }

    .page {
      padding: 40px 32px;
    }

    .ticket {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      background: #ffffff;
      border: 1px solid #e7e5e4;
      border-radius: 16px;
      overflow: hidden;
    }

    .top-strip {
      display: table;
      width: 100%;
      padding: 18px 28px;
      border-bottom: 1px solid #e7e5e4;
      background: #ffffff;
    }

    .brand {
      display: table-cell;
      vertical-align: middle;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 4px;
      color: #1c1917;
      text-transform: uppercase;
    }

    .brand .dot {
      display: inline-block;
      width: 8px;
      height: 8px;
      background: #ef4444;
      border-radius: 50%;
      margin-right: 10px;
      vertical-align: middle;
    }

    .top-meta {
      display: table-cell;
      vertical-align: middle;
      text-align: right;
      font-size: 10px;
      letter-spacing: 1.5px;
      color: #78716c;
      text-transform: uppercase;
    }

    .top-meta .id {
      display: inline-block;
      margin-left: 12px;
      color: #1c1917;
      font-weight: 600;
    }

    .main {
      display: table;
      width: 100%;
      padding: 32px 28px 28px;
    }

    .left {
      display: table-cell;
      vertical-align: top;
      width: 168px;
      padding-right: 24px;
    }

    .right {
      display: table-cell;
      vertical-align: top;
    }

    .poster {
      width: 168px;
      height: 232px;
      border-radius: 10px;
      overflow: hidden;
      background: #f5f5f4;
      border: 1px solid #e7e5e4;
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
      color: #a8a29e;
      font-size: 11px;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .poster-fallback span {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
    }

    .eyebrow {
      font-size: 10px;
      letter-spacing: 3px;
      color: #ef4444;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .film-title {
      font-size: 28px;
      line-height: 1.15;
      font-weight: 800;
      color: #1c1917;
      letter-spacing: -0.5px;
      margin-bottom: 6px;
    }

    .film-meta {
      font-size: 12px;
      color: #78716c;
      margin-bottom: 26px;
    }

    .grid {
      display: table;
      width: 100%;
      border-top: 1px solid #e7e5e4;
      padding-top: 18px;
    }

    .cell {
      display: table-cell;
      vertical-align: top;
      padding-right: 18px;
    }

    .cell:last-child {
      padding-right: 0;
    }

    .lbl {
      font-size: 9px;
      letter-spacing: 2px;
      color: #a8a29e;
      text-transform: uppercase;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .val {
      font-size: 15px;
      font-weight: 700;
      color: #1c1917;
      line-height: 1.25;
    }

    .bottom {
      display: table;
      width: 100%;
      background: #fafaf9;
      border-top: 1px solid #e7e5e4;
      padding: 22px 28px;
    }

    .b-left {
      display: table-cell;
      vertical-align: middle;
      width: 55%;
    }

    .b-right {
      display: table-cell;
      vertical-align: middle;
      text-align: right;
      width: 45%;
    }

    .seat-label {
      font-size: 9px;
      letter-spacing: 2px;
      color: #a8a29e;
      text-transform: uppercase;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .seats {
      font-size: 18px;
      font-weight: 700;
      color: #1c1917;
      letter-spacing: 1px;
    }

    .seats-sub {
      font-size: 11px;
      color: #78716c;
      margin-top: 4px;
    }

    .qr-box {
      display: inline-block;
      padding: 8px;
      background: #ffffff;
      border: 1px solid #e7e5e4;
      border-radius: 10px;
    }

    .qr-box img {
      width: 120px;
      height: 120px;
      display: block;
      image-rendering: pixelated;
      image-rendering: crisp-edges;
    }

    .qr-cap {
      font-size: 9px;
      letter-spacing: 2px;
      color: #78716c;
      text-transform: uppercase;
      margin-top: 8px;
      font-weight: 600;
    }

    .perf {
      position: relative;
      height: 1px;
      background-image: linear-gradient(to right, #d6d3d1 50%, transparent 50%);
      background-size: 8px 1px;
      background-repeat: repeat-x;
    }

    .stub {
      display: table;
      width: 100%;
      padding: 20px 28px 22px;
      background: #ffffff;
    }

    .stub-cell {
      display: table-cell;
      vertical-align: middle;
    }

    .stub-l {
      width: 60%;
    }

    .stub-r {
      text-align: right;
    }

    .barcode-wrap {
      display: inline-block;
      padding: 6px 10px;
      background: #ffffff;
      border: 1px solid #e7e5e4;
      border-radius: 6px;
    }

    .barcode-wrap img {
      max-width: 260px;
      max-height: 64px;
      display: block;
      image-rendering: pixelated;
    }

    .stub-order-label {
      font-size: 9px;
      letter-spacing: 2px;
      color: #a8a29e;
      text-transform: uppercase;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .stub-order-val {
      font-size: 14px;
      font-weight: 700;
      color: #1c1917;
      letter-spacing: 1px;
    }

    .meta {
      max-width: 800px;
      margin: 16px auto 0;
      text-align: center;
      font-size: 9px;
      color: #a8a29e;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
  </style>
</head>

<body>
  <div class="page">
    <div class="ticket">

      <div class="top-strip">
        <div class="brand"><span class="dot"></span>Ticketra &middot; Cinema Pass</div>
        <div class="top-meta">Booking ID<span class="id">{{ $booking->booking_id }}</span></div>
      </div>

      <div class="main">
        <div class="left">
          <div class="poster">
            @if($booking->jadwalTayang->film->poster_url)
            <img src="{{ $booking->jadwalTayang->film->poster_url }}" alt="poster">
            @else
            <div class="poster-fallback"><span>No Poster</span></div>
            @endif
          </div>
        </div>
        <div class="right">
          <div class="eyebrow">E-Ticket &middot; {{ $booking->jadwalTayang->film->genre ?? 'Cinema' }}</div>
          <div class="film-title">{{ $booking->jadwalTayang->film->judul }}</div>
          <div class="film-meta">
            {{ $booking->jadwalTayang->film->durasi ?? '—' }} min &middot;
            Rating {{ $booking->jadwalTayang->film->rating ?? 'SU' }}
          </div>

          <div class="grid">
            <div class="cell">
              <div class="lbl">Date</div>
              <div class="val">{{ $booking->jadwalTayang->waktu_mulai->translatedFormat('D, d M Y') }}</div>
            </div>
            <div class="cell">
              <div class="lbl">Time</div>
              <div class="val">{{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} WIB</div>
            </div>
            <div class="cell">
              <div class="lbl">Hall</div>
              <div class="val">Studio {{ $booking->jadwalTayang->studio->nama }}</div>
            </div>
            <div class="cell">
              <div class="lbl">Cinema</div>
              <div class="val">{{ $booking->jadwalTayang->studio->bioskop->nama }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="bottom">
        <div class="b-left">
          <div class="seat-label">Seats</div>
          <div class="seats">{{ $seatsList }}</div>
          <div class="seats-sub">{{ $booking->statusKursis->count() }} tiket &middot; 1 kali scan</div>
        </div>
        <div class="b-right">
          <div class="qr-box">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
          </div>
          <div class="qr-cap">Scan at entry</div>
        </div>
      </div>

      <div class="perf"></div>

      <div class="stub">
        <div class="stub-cell stub-l">
          <div class="barcode-wrap">
            <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
          </div>
        </div>
        <div class="stub-cell stub-r">
          <div class="stub-order-label">No. Order</div>
          <div class="stub-order-val">{{ $nomorOrder ?? $booking->booking_id }}</div>
        </div>
      </div>
    </div>

    <div class="meta">
      Dicetak {{ now()->format('d M Y, H:i') }} WIB &middot; Berlaku 1 kali scan &middot; Tidak dapat dicetak ulang
    </div>
  </div>
</body>

</html>