<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>E-Ticket {{ $booking->booking_id }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: #fff;
    }

    .ticket {
      border: 4px solid #000;
      border-radius: 12px;
      padding: 20px;
      max-width: 600px;
      margin: 0 auto;
    }

    .header {
      background: #FFB6C1;
      padding: 15px;
      text-align: center;
      border-bottom: 4px solid #000;
      margin: -20px -20px 20px -20px;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
    }

    .qr-section {
      text-align: center;
      margin: 20px 0;
      padding: 20px;
      background: #FFF3B0;
      border: 3px solid #000;
      border-radius: 8px;
    }

    .qr-section img {
      width: 200px;
      height: 200px;
      border: 3px solid #000;
      border-radius: 8px;
    }

    .booking-id {
      font-size: 18px;
      font-weight: bold;
      margin-top: 10px;
    }

    .section {
      margin: 20px 0;
    }

    .section-title {
      font-size: 16px;
      font-weight: bold;
      border-bottom: 2px solid #000;
      padding-bottom: 5px;
      margin-bottom: 10px;
    }

    .info-row {
      display: flex;
      margin: 8px 0;
    }

    .info-label {
      width: 120px;
      font-weight: bold;
      color: #666;
    }

    .info-value {
      flex: 1;
      font-weight: 600;
    }

    .seats {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin: 10px 0;
    }

    .seat {
      background: #D4B6FF;
      padding: 8px 16px;
      border: 2px solid #000;
      border-radius: 6px;
      font-weight: bold;
    }

    .price {
      background: #B6FFB6;
      padding: 15px;
      border: 3px solid #000;
      border-radius: 8px;
      margin: 15px 0;
    }

    .price-total {
      font-size: 24px;
      font-weight: bold;
      text-align: right;
      margin-top: 10px;
    }

    .footer {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 3px solid #000;
      text-align: center;
      font-size: 12px;
      color: #666;
    }
  </style>
</head>

<body>
  <div class="ticket">
    <div class="header">
      <h1>🎬 TICKETRA</h1>
      <p>E-TICKET BIOSKOP</p>
    </div>

    <div class="qr-section">
      <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
      <div class="booking-id">{{ $booking->booking_id }}</div>
      <div style="margin-top: 5px; color: green; font-weight: bold;">✓ CONFIRMED</div>
    </div>

    <div class="section">
      <div class="section-title">Detail Film</div>
      <div class="info-row">
        <div class="info-label">Judul</div>
        <div class="info-value">{{ $booking->jadwalTayang->film->judul }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Genre</div>
        <div class="info-value">{{ $booking->jadwalTayang->film->genre }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Durasi</div>
        <div class="info-value">{{ $booking->jadwalTayang->film->durasi_menit }} menit</div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Jadwal Tayang</div>
      <div class="info-row">
        <div class="info-label">Tanggal</div>
        <div class="info-value">{{ $booking->jadwalTayang->waktu_mulai->format('d M Y, H:i') }} WIB</div>
      </div>
      <div class="info-row">
        <div class="info-label">Bioskop</div>
        <div class="info-value">{{ $booking->jadwalTayang->studio->bioskop->nama }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Studio</div>
        <div class="info-value">{{ $booking->jadwalTayang->studio->nama }}</div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">💺 Kursi Terpilih</div>
      <div class="seats">
        @foreach($booking->statusKursis as $statusKursi)
        <div class="seat">{{ $statusKursi->kursi->label_baris }}{{ $statusKursi->kursi->nomor_kursi }}</div>
        @endforeach
      </div>
    </div>

    <div class="price">
      <div style="display: flex; justify-content: space-between;">
        <span>Total Pembayaran</span>
        <span style="font-size: 20px; font-weight: bold;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
      </div>
    </div>

    <div class="footer">
      <p>Terima kasih telah memesan di Ticketra</p>
      <p>Tunjukkan QR code ini saat masuk bioskop</p>
      <p style="margin-top: 10px;">www.ticketra.web.id</p>
    </div>
  </div>
</body>

</html>