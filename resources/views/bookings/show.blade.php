<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking {{ $booking->booking_code }} — NusantaraGuide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --forest: #1a3a2a; --jade: #2d6a4f; --lime: #74c69d; --cream: #fdf8f0; --gold: #e9c46a; --coral: #e76f51; --ink: #0d1b13; --muted: #6b7c72; --card-shadow: 0 4px 24px rgba(26,58,42,0.10); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--ink); }
        .navbar { position: sticky; top: 0; z-index: 100; background: rgba(26,58,42,0.97); backdrop-filter: blur(12px); padding: 0 6%; display: flex; align-items: center; justify-content: space-between; height: 64px; border-bottom: 1px solid rgba(116,198,157,0.15); }
        .logo { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 900; color: white; text-decoration: none; }
        .logo span { color: var(--gold); }
        .nav-links { display: flex; gap: 28px; list-style: none; }
        .nav-links a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 14px; font-weight: 500; }
        .nav-links a:hover { color: var(--lime); }
        .nav-user { display: flex; align-items: center; gap: 12px; }
        .nav-user span { color: rgba(255,255,255,0.7); font-size: 14px; }
        .logout-btn { background: transparent; border: 1px solid rgba(255,255,255,0.3); color: rgba(255,255,255,0.7); padding: 7px 16px; border-radius: 6px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 13px; }

        .page-wrap { max-width: 680px; margin: 48px auto; padding: 0 24px 80px; }

        /* SUCCESS BANNER */
        .success-banner {
            background: linear-gradient(135deg, var(--jade), var(--forest));
            border-radius: 18px; padding: 36px; text-align: center; margin-bottom: 28px; color: white;
        }
        .success-icon { font-size: 56px; margin-bottom: 14px; }
        .success-banner h1 { font-family: 'Playfair Display', serif; font-size: 26px; margin-bottom: 8px; }
        .success-banner p { font-size: 15px; opacity: 0.8; }
        .booking-code-badge {
            display: inline-block; background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 24px; border-radius: 20px; margin-top: 16px;
            font-size: 18px; font-weight: 700; letter-spacing: 2px;
        }

        /* STATUS */
        .status-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 600;
        }

        /* DETAIL CARD */
        .detail-card { background: white; border-radius: 18px; padding: 28px; box-shadow: var(--card-shadow); margin-bottom: 20px; }
        .detail-card h2 { font-size: 16px; font-weight: 700; color: var(--forest); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--cream); }
        .detail-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row .label { font-size: 13px; color: var(--muted); }
        .detail-row .value { font-size: 14px; font-weight: 600; color: var(--ink); text-align: right; max-width: 60%; }

        .total-highlight {
            background: var(--forest); color: white; border-radius: 12px;
            padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;
            margin-top: 4px;
        }
        .total-highlight .tl { font-size: 13px; opacity: 0.75; }
        .total-highlight .tv { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 900; }

        /* ACTIONS */
        .actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-primary { background: var(--jade); color: white; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .btn-primary:hover { background: var(--forest); }
        .btn-outline { background: white; color: var(--coral); border: 1.5px solid var(--coral); padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
        .btn-outline:hover { background: var(--coral); color: white; }
        .btn-ghost { background: white; color: var(--muted); border: 1.5px solid #e0dbd2; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.2s; }
        .btn-ghost:hover { border-color: var(--jade); color: var(--jade); }

        footer { background: var(--ink); color: rgba(255,255,255,0.5); padding: 32px 8%; text-align: center; font-size: 14px; }
        footer strong { color: var(--lime); }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="logo">Nusantara<span>Guide</span></a>
    <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('bookings.my') }}">Booking Saya</a></li>
    </ul>
    <div class="nav-user">
        <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</nav>

<div class="page-wrap">

    @if(session('success'))
    <div class="success-banner">
        <div class="success-icon">🎉</div>
        <h1>Pemesanan Berhasil!</h1>
        <p>Booking kamu sudah masuk dan sedang menunggu konfirmasi dari hotel.</p>
        <div class="booking-code-badge">{{ $booking->booking_code }}</div>
    </div>
    @endif

    <!-- STATUS -->
    <div class="detail-card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">Kode Booking</div>
            <div style="font-size:20px;font-weight:700;color:var(--ink);letter-spacing:1px;">{{ $booking->booking_code }}</div>
        </div>
        <span class="status-badge" style="background:{{ $booking->status_color }}22;color:{{ $booking->status_color }};border:1px solid {{ $booking->status_color }}44;">
            {{ $booking->status_label }}
        </span>
    </div>

    <!-- HOTEL & ROOM -->
    <div class="detail-card">
        <h2>Detail Hotel</h2>
        <div class="detail-row">
            <span class="label">Hotel</span>
            <span class="value">
                <a href="{{ route('place.show', $booking->place_id) }}" style="color:var(--jade);">{{ $booking->place->name }}</a>
            </span>
        </div>
        <div class="detail-row">
            <span class="label">Tipe Kamar</span>
            <span class="value">{{ $booking->room->name }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Alamat</span>
            <span class="value">{{ $booking->place->address }}</span>
        </div>
    </div>

    <!-- BOOKING DETAILS -->
    <div class="detail-card">
        <h2>Detail Pemesanan</h2>
        <div class="detail-row">
            <span class="label">Check-in</span>
            <span class="value">{{ $booking->check_in->isoFormat('dddd, D MMMM YYYY') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Check-out</span>
            <span class="value">{{ $booking->check_out->isoFormat('dddd, D MMMM YYYY') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Durasi</span>
            <span class="value">{{ $booking->nights }} malam</span>
        </div>
        <div class="detail-row">
            <span class="label">Jumlah Tamu</span>
            <span class="value">{{ $booking->guests }} orang</span>
        </div>
        @if($booking->special_requests)
        <div class="detail-row">
            <span class="label">Permintaan Khusus</span>
            <span class="value">{{ $booking->special_requests }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="label">Harga per malam</span>
            <span class="value">{{ $booking->room->price_label }}</span>
        </div>
        <div class="total-highlight">
            <span class="tl">Total Pembayaran</span>
            <span class="tv">{{ $booking->total_label }}</span>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="detail-card">
        <div class="actions">
            <a href="{{ route('bookings.my') }}" class="btn-primary">📋 Semua Booking Saya</a>
            <a href="{{ route('place.show', $booking->place_id) }}" class="btn-ghost">Kembali ke Hotel</a>
            @if($booking->status === 'pending')
                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                    @csrf
                    <button type="submit" class="btn-outline">Batalkan Booking</button>
                </form>
            @endif
        </div>
    </div>

</div>

<footer>
    <p>© {{ date('Y') }} NusantaraGuide — Panduan wisata seluruh <strong>Indonesia</strong></p>
</footer>
</body>
</html>