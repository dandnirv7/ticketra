@php
  $qrCode = $qrCode ?? ($qrCodeBase64 ?? '');
  $nomorOrder = $nomorOrder ?? ($booking->paymentWebhooks()->orderBy('created_at')->value('transaction_id') ?? $booking->booking_id);
  $seatsList = $seatsList ?? $booking->statusKursis->map(fn($sk) => $sk->kursi->label_baris . $sk->kursi->nomor_kursi)->implode(', ');
  $genre = $genre ?? ($booking->jadwalTayang->film->genre ?? 'Cinema');

  if (!isset($barcode)) {
      try {
          $barcode = Milon\Barcode\DNS1DFacade::getBarcodePNG($booking->booking_id, "C128", 2, 90, [0, 0, 0]);
      } catch (\Exception $e) {
          $barcode = null;
      }
  }

  $logoPath = public_path('favicon/favicon-96x96.png');
  $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

  $posterBase64 = null;
  if ($booking->jadwalTayang->film->poster_url) {
      $posterUrl = $booking->jadwalTayang->film->poster_url;
      try {
          if (str_starts_with($posterUrl, 'http://') || str_starts_with($posterUrl, 'https://')) {
              $ctx = stream_context_create([
                  'http' => [
                      'timeout' => 8,
                  ],
                  'ssl' => [
                      'verify_peer' => false,
                      'verify_peer_name' => false,
                  ]
              ]);
              $imgData = @file_get_contents($posterUrl, false, $ctx);
              if ($imgData) {
                  $posterBase64 = base64_encode($imgData);
              }
          } else {
              $cleanPath = ltrim($posterUrl, '/');
              $localPath = public_path($cleanPath);
              if (file_exists($localPath)) {
                  $posterBase64 = base64_encode(file_get_contents($localPath));
              } else {
                  $storageSub = str_replace('storage/', '', $cleanPath);
                  $storagePath = storage_path('app/public/' . $storageSub);
                  if (file_exists($storagePath)) {
                      $posterBase64 = base64_encode(file_get_contents($storagePath));
                  }
              }
          }
      } catch (\Exception $e) {
      }
  }

  $ageRating = $booking->jadwalTayang->film->rating_usia ?? 'SU';
  $ageRatingLabel = match(strtoupper($ageRating)) {
      'SU' => 'Semua Umur',
      '13+' => '13 Tahun Keatas',
      '17+' => '17 Tahun Keatas',
      '21+' => '21 Tahun Keatas',
      default => 'Semua Umur'
  };

  $studioName = $booking->jadwalTayang->studio->nama;
  if (!str_starts_with(strtolower($studioName), 'studio')) {
      $studioName = 'Studio ' . $studioName;
  }
@endphp
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
    /* Set only standard font names recognized by Dompdf (helvetica, sans-serif) without custom quotes to avoid serif fallback */
    html, body, table, tr, td, div, span, p {
      font-family: helvetica, sans-serif !important;
    }
    body {
      background-color: #EEEAF8;
      color: #111827;
      padding: 30px 24px;
    }
    .container {
      width: 100%;
      max-width: 680px;
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <div class="container">

    <table style="width: 100%; border-collapse: collapse; border: 1.5px solid #E5E7EB; border-radius: 20px; background-color: #ffffff; overflow: hidden; table-layout: fixed;">

      <tr>
        <td style="padding: 24px 24px 18px 24px; border-bottom: 1.2px solid #F3F4F6;">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="vertical-align: middle;">
                <table style="border-collapse: collapse;">
                  <tr>
                    @if($logoBase64)
                      <td style="padding-right: 10px; vertical-align: middle;">
                        <img src="data:image/png;base64,{{ $logoBase64 }}" style="width: 36px; height: 36px; display: block; border-radius: 9px;" alt="Logo">
                      </td>
                    @endif
                    <td style="vertical-align: middle;">
                      <span style="font-size: 28px; font-weight: bolder; color: #111827; letter-spacing: -0.5px;">Ticketra<span style="color: #7b4ce6;">.</span></span>
                    </td>
                  </tr>
                </table>
              </td>
              <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 9px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px;">Booking ID</div>
                <div style="font-size: 13px; font-weight: bold; color: #7b4ce6;">{{ $booking->booking_id }}</div>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td style="padding: 24px 24px 20px 24px;">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="width: 140px; vertical-align: top; padding-right: 24px;">
                @if($posterBase64)
                  <img src="data:image/png;base64,{{ $posterBase64 }}" style="width: 140px; height: 196px; border-radius: 12px; object-fit: cover; display: block; border: 1.2px solid #E5E7EB;" alt="Poster">
                @elseif($booking->jadwalTayang->film->poster_url)
                  <img src="{{ $booking->jadwalTayang->film->poster_url }}" style="width: 140px; height: 196px; border-radius: 12px; object-fit: cover; display: block; border: 1.2px solid #E5E7EB;" alt="Poster">
                @else
                  <div style="width: 140px; height: 196px; border-radius: 12px; background-color: #F3F4F6; border: 1.2px solid #E5E7EB; text-align: center; display: table;">
                    <span style="display: table-cell; vertical-align: middle; color: #9CA3AF; font-size: 11px; text-transform: uppercase; font-weight: bold;">No Poster</span>
                  </div>
                @endif
              </td>

              <td style="vertical-align: top; padding-top: 4px;">

                <table style="border-collapse: collapse; margin-bottom: 12px;">
                  <tr>
                    <td style="border: 1.5px solid #7b4ce6; border-radius: 6px; padding: 3px 8px; font-size: 9px; font-weight: bold; color: #7b4ce6; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: middle; line-height: 1;">
                      E-Ticket
                    </td>
                    <td style="padding-left: 12px; font-size: 11px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.8px; vertical-align: middle; line-height: 1;">
                      {{ $genre }}
                    </td>
                  </tr>
                </table>

                <div style="font-size: 23px; font-weight: bold; color: #111827; margin-bottom: 12px; line-height: 1.2; letter-spacing: -0.5px;">
                  {{ $booking->jadwalTayang->film->judul }}
                </div>

                <table style="border-collapse: collapse;">
                  <tr>
                    <td style="border: 1.5px solid #9CA3AF; border-radius: 5px; padding: 2px 7px; font-size: 10px; font-weight: bold; color: #4B5563; vertical-align: middle; line-height: 1;">
                      {{ $ageRating }}
                    </td>
                    <td style="padding-left: 8px; font-size: 12.5px; font-weight: 600; color: #6B7280; vertical-align: middle; line-height: 1;">
                      {{ $ageRatingLabel }} &nbsp;&bull;&nbsp; {{ $booking->jadwalTayang->film->durasi_menit ?? '—' }} Menit
                    </td>
                  </tr>
                </table>

                <div style="border-top: 1.2px solid #F3F4F6; margin: 16px 0 14px 0; width: 100%;"></div>

                <table style="width: 100%; border-collapse: collapse;">
                  <tr>
                    <td style="width: 25%; vertical-align: top; padding-right: 6px;">
                      <table style="border-collapse: collapse; margin-bottom: 4px;">
                        <tr>
                          <td style="vertical-align: middle; padding-right: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7b4ce6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                              <line x1="16" y1="2" x2="16" y2="6"></line>
                              <line x1="8" y1="2" x2="8" y2="6"></line>
                              <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                          </td>
                          <td style="vertical-align: middle; font-size: 8px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1;">
                            Tanggal
                          </td>
                        </tr>
                      </table>
                      <div style="font-size: 12px; font-weight: bold; color: #111827; line-height: 1.2;">{{ $booking->jadwalTayang->waktu_mulai->translatedFormat('D, d M Y') }}</div>
                    </td>

                    <td style="width: 25%; vertical-align: top; padding-right: 6px; padding-left: 6px;">
                      <table style="border-collapse: collapse; margin-bottom: 4px;">
                        <tr>
                          <td style="vertical-align: middle; padding-right: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7b4ce6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                              <circle cx="12" cy="12" r="10"></circle>
                              <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                          </td>
                          <td style="vertical-align: middle; font-size: 8px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1;">
                            Waktu
                          </td>
                        </tr>
                      </table>
                      <div style="font-size: 12px; font-weight: bold; color: #111827; line-height: 1.2;">{{ $booking->jadwalTayang->waktu_mulai->format('H:i') }} WIB</div>
                    </td>

                    <td style="width: 25%; vertical-align: top; padding-right: 6px; padding-left: 6px;">
                      <table style="border-collapse: collapse; margin-bottom: 4px;">
                        <tr>
                          <td style="vertical-align: middle; padding-right: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7b4ce6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                              <rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect>
                              <polyline points="17 2 12 7 7 2"></polyline>
                              <line x1="2" y1="11" x2="22" y2="11"></line>
                            </svg>
                          </td>
                          <td style="vertical-align: middle; font-size: 8px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1;">
                            Studio
                          </td>
                        </tr>
                      </table>
                      <div style="font-size: 12px; font-weight: bold; color: #111827; line-height: 1.2;">{{ $studioName }}</div>
                    </td>

                    <td style="width: 25%; vertical-align: top; padding-left: 6px;">
                      <table style="border-collapse: collapse; margin-bottom: 4px;">
                        <tr>
                          <td style="vertical-align: middle; padding-right: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7b4ce6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                              <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                          </td>
                          <td style="vertical-align: middle; font-size: 8px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1;">
                            Bioskop
                          </td>
                        </tr>
                      </table>
                      <div style="font-size: 12px; font-weight: bold; color: #111827; line-height: 1.2;">{{ $booking->jadwalTayang->studio->bioskop->nama }}</div>
                    </td>
                  </tr>
                </table>

              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td style="padding: 0 24px;">
          <div style="border-top: 1.2px solid #F3F4F6; width: 100%;"></div>
        </td>
      </tr>

      <tr>
        <td style="padding: 24px 24px 20px 24px;">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="vertical-align: top; padding-right: 24px;">
                <table style="border-collapse: collapse; margin-bottom: 6px;">
                  <tr>
                    <td style="vertical-align: middle; padding-right: 6px;">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7b4ce6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 19v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path>
                        <path d="M6 11V7a6 6 0 0 1 12 0v4"></path>
                        <line x1="6" y1="19" x2="6" y2="21"></line>
                        <line x1="18" y1="19" x2="18" y2="21"></line>
                      </svg>
                    </td>
                    <td style="font-size: 10px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; vertical-align: middle; line-height: 1;">
                      Kursi Anda
                    </td>
                  </tr>
                </table>

                <div style="font-size: 48px; font-weight: bold; color: #7b4ce6; line-height: 1; margin-bottom: 8px;">
                  {{ $seatsList }}
                </div>

                <div style="font-size: 12px; font-weight: 500; color: #6B7280; margin-bottom: 24px;">
                  {{ $booking->statusKursis->count() }} tiket &nbsp;&bull;&nbsp; 1 kali scan
                </div>

                <table style="width: 100%; border-collapse: collapse; border: 1.2px solid #E5E7EB; border-radius: 12px; background-color: #ffffff; padding: 10px 14px;">
                  <tr>
                    @if($barcode)
                      <td style="width: 130px; vertical-align: middle; padding-right: 12px; border-right: 1.2px solid #F3F4F6;">
                        <img src="data:image/png;base64,{{ $barcode }}" style="width: 130px; height: 32px; display: block;" alt="Barcode">
                      </td>
                    @endif
                    <td style="vertical-align: middle; padding-left: 12px;">
                      <div style="font-size: 8px; font-weight: bold; color: #7b4ce6; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 3px;">No. Order</div>
                      <div style="font-size: 10px; font-weight: bold; color: #374151; word-break: break-all;">{{ $nomorOrder }}</div>
                    </td>
                  </tr>
                </table>
              </td>

              <td style="width: 1px; border-left: 1.5px dashed #D1D5DB; vertical-align: stretch;"></td>

              <td style="width: 172px; text-align: right; vertical-align: middle; padding-left: 24px;">
                <div style="display: inline-block; border: 1.2px solid #E5E7EB; border-radius: 14px; padding: 12px; background-color: #ffffff; text-align: center;">
                  <img src="data:image/png;base64,{{ $qrCode }}" style="width: 120px; height: 120px; display: block;" alt="QR Code">
                  <div style="font-size: 9px; font-weight: bold; color: #7b4ce6; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 10px;">Scan at Entry</div>
                </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td style="background-color: #F4F0FF; padding: 14px 24px; border-bottom-left-radius: 18px; border-bottom-right-radius: 18px; border-top: 1.2px solid #ECE9F1; text-align: center;">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="text-align: center; vertical-align: middle; font-size: 10.5px; font-weight: bold; color: #7b4ce6;">
                @if($logoBase64)
                  <img src="data:image/png;base64,{{ $logoBase64 }}" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px; border-radius: 5px;" alt="Logo">
                @endif
                <span style="vertical-align: middle;">
                  Dicetak {{ now()->translatedFormat('d M Y, H:i') }} WIB
                  <span style="color: #D1D5DB; margin: 0 8px;">&bull;</span>
                  Berlaku 1 kali scan
                  <span style="color: #D1D5DB; margin: 0 8px;">&bull;</span>
                  Tidak dapat dicetak ulang
                </span>
              </td>
            </tr>
          </table>
        </td>
      </tr>

    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
      <tr>
        <td style="text-align: center;">
          @if($logoBase64)
            <div style="margin-bottom: 8px;">
              <img src="data:image/png;base64,{{ $logoBase64 }}" style="width: 28px; height: 28px; display: inline-block; border-radius: 8px;" alt="Logo">
            </div>
          @endif
          <div style="font-size: 12px; font-weight: 500; color: #4B5563; line-height: 1.6;">
            Terima kasih telah memilih Ticketra.<br>
            <span style="color: #7b4ce6; font-weight: bold;">Selamat menikmati filmnya!</span>
          </div>
        </td>
      </tr>
    </table>

  </div>
</body>
</html>
