<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>E-Ticket {{ $booking->booking_id }}</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      margin: 0;
      padding: 10px;
      background: #FFFFFF;
      color: #1F2937;
    }

    .ticket {
      max-width: 500px;
      margin: 0 auto;
      background: #FFFFFF;
      padding: 30px;
      border: 1px solid #ECECEC;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .header {
      text-align: center;
      margin-bottom: 25px;
      padding-bottom: 20px;
      border-bottom: 2px dashed #ECECEC;
    }

    .brand-title {
      font-size: 24px;
      font-weight: 800;
      letter-spacing: -0.5px;
      color: #A88CF8;
      margin: 0 0 4px 0;
    }

    .ticket-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: #A88CF8;
      margin: 0;
    }

    .qr-section {
      text-align: center;
      margin: 25px 0;
      padding: 20px;
      background: #FFFFFF;
      border: 1px solid #ECECEC;
      border-radius: 12px;
    }

    .qr-section img {
      width: 160px;
      height: 160px;
      margin-bottom: 15px;
    }

    .booking-id {
      font-size: 16px;
      font-weight: 700;
      color: #1F2937;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }

    .scan-instruction {
      font-size: 11px;
      font-weight: 600;
      color: #9CA3AF;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .section {
      margin: 20px 0;
    }

    .section-title {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #9CA3AF;
      border-bottom: 1px solid #ECECEC;
      padding-bottom: 6px;
      margin-bottom: 12px;
    }

    .info-row {
      display: flex;
      margin: 8px 0;
      font-size: 14px;
    }

    .info-label {
      width: 100px;
      color: #9CA3AF;
      font-weight: 500;
    }

    .info-value {
      flex: 1;
      font-weight: 600;
      color: #1F2937;
    }

    .seats {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin: 10px 0;
    }

    .seat {
      background: #FAF8FF;
      color: #A88CF8;
      border: 1px solid #CDBBFF;
      padding: 6px 14px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 13px;
    }

    .price {
      background: #FAF8FF;
      border: 1px solid #ECECEC;
      padding: 16px;
      border-radius: 12px;
      margin-top: 25px;
    }

    .price-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .price-label {
      font-size: 13px;
      font-weight: 600;
      color: #9CA3AF;
    }

    .price-value {
      font-size: 20px;
      font-weight: 800;
      color: #A88CF8;
    }

    .footer {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #ECECEC;
      text-align: center;
      font-size: 11px;
      color: #9CA3AF;
      line-height: 1.6;
    }
  </style>
</head>

<body>
  <div class="ticket">
    <div class="header">
      <div class="brand-title">Ticketra.</div>
      <div class="ticket-label">E-Ticket Bioskop</div>
    </div>

    <div class="qr-section">
      <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
      <div class="booking-id">{{ $booking->booking_id }}</div>
      <div class="scan-instruction">Scan at Entry</div>
    </div>

    <div class="section">
      <div class="section-title">Detail Nonton</div>
      <div class="info-row">
        <div class="info-label">Film</div>
        <div class="info-value">{{ $booking->jadwalTayang->film->judul }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Bioskop</div>
        <div class="info-value">{{ $booking->jadwalTayang->studio->bioskop->nama }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Studio</div>
        <div class="info-value">{{ $booking->jadwalTayang->studio->nama }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Waktu</div>
        <div class="info-value">{{ $booking->jadwalTayang->waktu_mulai->format('d M Y, H:i') }} WIB</div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">💺 Kursi Anda</div>
      <div class="seats">
        @foreach($booking->statusKursis as $statusKursi)
        <div class="seat">{{ $statusKursi->kursi->label_baris }}{{ $statusKursi->kursi->nomor_kursi }}</div>
        @endforeach
      </div>
    </div>

    <div class="price">
      <div class="price-row">
        <div class="price-label">Total Pembayaran</div>
        <div class="price-value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
      </div>
    </div>

    <div class="footer">
      <p>Tunjukkan E-Ticket ini ke petugas bioskop untuk scan entry.<br>
         Kunjungi <strong>www.ticketra.web.id</strong> untuk informasi pemesanan Anda.</p>
    </div>
  </div>
</body>

</html>
