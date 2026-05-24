<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Saya — NusantaraGuide</title>
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
        .nav-links a:hover, .nav-links a.active { color: var(--lime); }
        .nav-user { display: flex; align-items: center; gap: 12px; }
        .nav-user span { color: rgba(255,255,255,0.7); font-size: 14px; }
        .logout-btn { background: transparent; border: 1px solid rgba(255,255,255,0.3); color: rgba(255,255,255,0.7); padding: 7px 16px; border-radius: 6px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 13px; }

        .page-hero { background: linear-gradient(135deg, var(--forest), var(--jade)); padding: 48px 8% 40px; }
        .page-hero h1 { font-family: 'Playfair Display', serif; font-size: 36px; color: white; font-weight: 900; margin-bottom: 6px; }
        .page-hero p { font-size: 15px; color: rgba(255,255,255,0.7); }

        .main { padding: 40px 8%; max-width: 1100px; }

        .alert-success { background: rgba(116,198,157,0.15); border: 1px solid var(--lime); color: var(--jade); padding: 14px 18px; border-radius: 10px; font-size: 14px; margin-bottom: 24px; }

        /* BOOKING CARDS */
        .booking-list { display: flex; flex-direction: column; gap: 18px; }
        .booking-card {
            background: white; border-radius: 16px; box-shadow: var(--card-shadow);
            overflow: hidden; display: grid; grid-template-columns: 140px 1fr auto;
            transition: box-shadow 0.2s;
        }
        .booking-card:hover { box-shadow: 0 8px 32px rgba(26,58,42,0.15); }
        .booking-card-img { width: 140px; height: 100%; object-fit: cover; background: var(--cream); display: block; }
        .booking-card-body { padding: 20px 24px; }
        .booking-hotel { font-size: 17px; font-weight: 700; color: var(--ink); margin-bottom: 3px; }
        .booking-room  { font-size: 13px; color: var(--jade); font-weight: 600; margin-bottom: 12px; }
        .booking-meta  { display: flex; gap: 20px; flex-wrap: wrap; }
        .booking-meta-item { font-size: 13px; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .booking-meta-item strong { color: var(--ink); font-weight: 600; }
        .booking-card-right { padding: 20px; display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; min-width: 180px; border-left: 1px solid #f5f5f5; }
        .status-pill { padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap; }
        .booking-total { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 900; color: var(--forest); }
        .booking-nights { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .booking-actions { display: flex; flex-direction: column; gap: 8px; width: 100%; }
        .btn-sm { padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer; font-family: 'DM Sans', sans-serif; border: none; transition: all 0.2s; display: block; }
        .btn-sm-primary { background: var(--jade); color: white; }
        .btn-sm-primary:hover { background: var(--forest); }
        .btn-sm-danger { background: white; color: var(--coral); border: 1.5px solid var(--coral); }
        .btn-sm-danger:hover { background: var(--coral); color: white; }

        /* EMPTY */
        .empty-state { text-align: center; padding: 80px 20px; }
        .empty-state .icon { font-size: 56px; margin-bottom: 16px; }
        .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 22px; color: var(--ink); margin-bottom: 8px; }
        .empty-state p { font-size: 15px; color: var(--muted); margin-bottom: 24px; }
        .btn-explore { background: var(--jade); color: white; padding: 13px 32px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block; }

        .pagination-wrap { display: flex; justify-content: center; margin-top: 32px; }
        footer { background: var(--ink); color: rgba(255,255,255,0.5); padding: 32px 8%; text-align: center; font-size: 14px; margin-top: 60px; }
        footer strong { color: var(--lime); }

        @media (max-width: 700px) {
            .booking-card { grid-template-columns: 1fr; }
            .booking-card-img { width: 100%; height: 160px; }
            .booking-card-right { border-left: none; border-top: 1px solid #f5f5f5; flex-direction: row; align-items: center; }
            .booking-actions { flex-direction: row; width: auto; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="logo">Nusantara<span>Guide</span></a>
    <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('places.browse') }}">Tempat</a></li>
        <li><a href="{{ route('bookings.my') }}" class="active">Booking Saya</a></li>
    </ul>
    <div class="nav-user">
        <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</nav>

<div class="page-hero">
    <h1>Booking Saya 🏨</h1>
    <p>Riwayat dan status pemesanan hotelmu.</p>
</div>

<div class="main">
    @if(session('success'))
        <div class="alert-success">✓ {{ session('success') }}</div>
    @endif

    @if($bookings->count())
    <div class="booking-list">
        @foreach($bookings as $booking)
        <div class="booking-card">
            <img src="{{ asset($booking->place->image_url ?? 'images/default.jpg') }}"
                 class="booking-card-img" alt="{{ $booking->place->name }}"
                 onerror="this.style.background='#e8f4ee';this.style.display='none';">

            <div class="booking-card-body">
                <div class="booking-hotel">{{ $booking->place->name }}</div>
                <div class="booking-room">🛏️ {{ $booking->room->name }}</div>
                <div class="booking-meta">
                    <div class="booking-meta-item">
                        📅 <strong>{{ $booking->check_in->isoFormat('D MMM Y') }}</strong>
                        → <strong>{{ $booking->check_out->isoFormat('D MMM Y') }}</strong>
                    </div>
                    <div class="booking-meta-item">
                        👥 {{ $booking->guests }} tamu
                    </div>
                    <div class="booking-meta-item">
                        🔖 {{ $booking->booking_code }}
                    </div>
                </div>
            </div>

            <div class="booking-card-right">
                <span class="status-pill"
                      style="background:{{ $booking->status_color }}22;color:{{ $booking->status_color }};border:1px solid {{ $booking->status_color }}44;">
                    {{ $booking->status_label }}
                </span>
                <div>
                    <div class="booking-total">{{ $booking->total_label }}</div>
                    <div class="booking-nights">{{ $booking->nights }} malam</div>
                </div>
                <div class="booking-actions">
                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn-sm btn-sm-primary">Lihat Detail</a>
                    @if($booking->status === 'pending')
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST"
                              onsubmit="return confirm('Yakin batalkan booking {{ $booking->booking_code }}?')">
                            @csrf
                            <button type="submit" class="btn-sm btn-sm-danger" style="width:100%;">Batalkan</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-wrap">{{ $bookings->links() }}</div>

    @else
    <div class="empty-state">
        <div class="icon">🏨</div>
        <h3>Belum ada booking</h3>
        <p>Kamu belum pernah memesan hotel melalui NusantaraGuide.</p>
        <a href="{{ route('places.browse', ['category' => 3]) }}" class="btn-explore">Cari Hotel →</a>
    </div>
    @endif
</div>

<footer>
    <p>© {{ date('Y') }} NusantaraGuide — Panduan wisata seluruh <strong>Indonesia</strong></p>
</footer>
</body>
</html>