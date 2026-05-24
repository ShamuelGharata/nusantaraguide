<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kamar — {{ $place->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --forest: #1a3a2a; --jade: #2d6a4f; --lime: #74c69d;
            --cream: #fdf8f0; --gold: #e9c46a; --coral: #e76f51;
            --ink: #0d1b13; --muted: #6b7c72;
            --card-shadow: 0 4px 24px rgba(26,58,42,0.10);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--ink); }

        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(26,58,42,0.97); backdrop-filter: blur(12px);
            padding: 0 6%; display: flex; align-items: center;
            justify-content: space-between; height: 64px;
            border-bottom: 1px solid rgba(116,198,157,0.15);
        }
        .logo { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 900; color: white; text-decoration: none; }
        .logo span { color: var(--gold); }
        .nav-links { display: flex; gap: 28px; list-style: none; }
        .nav-links a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 14px; font-weight: 500; }
        .nav-links a:hover { color: var(--lime); }
        .nav-user { display: flex; align-items: center; gap: 12px; }
        .nav-user span { color: rgba(255,255,255,0.7); font-size: 14px; }
        .logout-btn { background: transparent; border: 1px solid rgba(255,255,255,0.3); color: rgba(255,255,255,0.7); padding: 7px 16px; border-radius: 6px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 13px; }

        /* PAGE */
        .page-wrap { max-width: 1000px; margin: 48px auto; padding: 0 24px 80px; }
        .breadcrumb { font-size: 13px; color: var(--muted); display: flex; align-items: center; gap: 8px; margin-bottom: 28px; flex-wrap: wrap; }
        .breadcrumb a { color: var(--jade); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .page-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 900; color: var(--forest); margin-bottom: 32px; }

        /* LAYOUT */
        .booking-layout { display: grid; grid-template-columns: 1fr 360px; gap: 32px; }

        /* FORM */
        .form-card { background: white; border-radius: 18px; padding: 32px; box-shadow: var(--card-shadow); }
        .form-card h2 { font-size: 18px; font-weight: 700; color: var(--forest); margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid var(--cream); }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 7px; letter-spacing: 0.3px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 12px 16px; border: 1.5px solid #e0dbd2; border-radius: 10px;
            font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--ink);
            background: var(--cream); outline: none; transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--jade); background: white; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .error-msg { color: var(--coral); font-size: 12px; margin-top: 5px; }

        .submit-btn {
            width: 100%; padding: 15px; background: var(--jade); color: white;
            border: none; border-radius: 12px; font-family: 'DM Sans', sans-serif;
            font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s;
            margin-top: 8px;
        }
        .submit-btn:hover { background: var(--forest); transform: translateY(-2px); }

        /* SUMMARY CARD */
        .summary-card { background: white; border-radius: 18px; padding: 28px; box-shadow: var(--card-shadow); position: sticky; top: 88px; }
        .summary-card h3 { font-size: 16px; font-weight: 700; color: var(--forest); margin-bottom: 20px; }

        .hotel-thumb { width: 100%; height: 160px; object-fit: cover; border-radius: 12px; margin-bottom: 18px; background: var(--cream); display: block; }

        .summary-hotel { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
        .summary-room  { font-size: 13px; color: var(--jade); font-weight: 600; margin-bottom: 16px; }

        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 14px; }
        .summary-row:last-of-type { border-bottom: none; }
        .summary-row .label { color: var(--muted); }
        .summary-row .value { font-weight: 600; color: var(--ink); }

        .total-box { background: var(--forest); color: white; border-radius: 12px; padding: 18px; margin-top: 16px; }
        .total-box .total-label { font-size: 12px; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .total-box .total-value { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 900; }
        .total-box .total-note  { font-size: 12px; opacity: 0.6; margin-top: 4px; }

        .policy-note { font-size: 12px; color: var(--muted); margin-top: 14px; line-height: 1.6; }
        .policy-note strong { color: var(--ink); }

        footer { background: var(--ink); color: rgba(255,255,255,0.5); padding: 32px 8%; text-align: center; font-size: 14px; }
        footer strong { color: var(--lime); }

        @media (max-width: 800px) {
            .booking-layout { grid-template-columns: 1fr; }
            .summary-card { position: static; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="logo">Nusantara<span>Guide</span></a>
    <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('places.browse') }}">Tempat</a></li>
        <li><a href="{{ route('itinerary.index') }}">Itinerary</a></li>
    </ul>
    <div class="nav-user">
        <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</nav>

<div class="page-wrap">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a> /
        <a href="{{ route('place.show', $place->id) }}">{{ $place->name }}</a> /
        <span>Pesan Kamar</span>
    </div>

    <div class="page-title">Pesan Kamar 🏨</div>

    <div class="booking-layout">

        <!-- FORM -->
        <div class="form-card">
            <h2>Detail Pemesanan</h2>

            @if($errors->any())
                <div style="background:rgba(231,111,81,0.1);border:1px solid var(--coral);border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:var(--coral);">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">

                <div class="form-row">
                    <div class="form-group">
                        <label>Check-in</label>
                        <input type="date" name="check_in" id="check_in"
                               value="{{ old('check_in') }}"
                               min="{{ date('Y-m-d') }}" required
                               onchange="calculateTotal()">
                        @error('check_in')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Check-out</label>
                        <input type="date" name="check_out" id="check_out"
                               value="{{ old('check_out') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                               onchange="calculateTotal()">
                        @error('check_out')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Jumlah Tamu</label>
                    <select name="guests" id="guests" onchange="calculateTotal()">
                        @for($i = 1; $i <= $room->max_guests; $i++)
                            <option value="{{ $i }}" {{ old('guests') == $i ? 'selected' : '' }}>
                                {{ $i }} tamu
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label>Permintaan Khusus <span style="font-weight:400;color:var(--muted);">(opsional)</span></label>
                    <textarea name="special_requests" placeholder="Contoh: kamar lantai atas, extra bed, dll...">{{ old('special_requests') }}</textarea>
                </div>

                <!-- Booker info (read-only, from logged in user) -->
                <div style="background:var(--cream);border-radius:10px;padding:16px;margin-bottom:20px;">
                    <div style="font-size:12px;font-weight:700;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:10px;">Data Pemesan</div>
                    <div style="font-size:14px;font-weight:600;color:var(--ink);">{{ Auth::user()->name }}</div>
                    <div style="font-size:13px;color:var(--muted);">{{ Auth::user()->email }}</div>
                </div>

                <button type="submit" class="submit-btn">Konfirmasi Pemesanan →</button>
            </form>
        </div>

        <!-- SUMMARY -->
        <div>
            <div class="summary-card">
                <h3>Ringkasan Pemesanan</h3>
                <img src="{{ asset($place->image_url ?? 'images/default.jpg') }}"
                     class="hotel-thumb" alt="{{ $place->name }}"
                     onerror="this.style.background='#e8f4ee';this.style.display='none';">
                <div class="summary-hotel">{{ $place->name }}</div>
                <div class="summary-room">🛏️ {{ $room->name }}</div>

                <div class="summary-row">
                    <span class="label">Harga per malam</span>
                    <span class="value">{{ $room->price_label }}</span>
                </div>
                <div class="summary-row">
                    <span class="label">Maks. tamu</span>
                    <span class="value">{{ $room->max_guests }} orang</span>
                </div>
                <div class="summary-row" id="nights-row" style="display:none;">
                    <span class="label">Jumlah malam</span>
                    <span class="value" id="nights-display">-</span>
                </div>

                <div class="total-box">
                    <div class="total-label">Total Harga</div>
                    <div class="total-value" id="total-display">Pilih tanggal</div>
                    <div class="total-note" id="total-note"></div>
                </div>

                <p class="policy-note">
                    <strong>Kebijakan pembatalan:</strong> Pemesanan dapat dibatalkan selama status masih <em>Menunggu Konfirmasi</em>. Setelah dikonfirmasi, hubungi hotel langsung.
                </p>
            </div>
        </div>

    </div>
</div>

<footer>
    <p>© {{ date('Y') }} NusantaraGuide — Panduan wisata seluruh <strong>Indonesia</strong></p>
</footer>

<script>
const pricePerNight = {{ $room->price_per_night }};

function calculateTotal() {
    const checkIn  = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;

    if (!checkIn || !checkOut) return;

    const d1 = new Date(checkIn);
    const d2 = new Date(checkOut);
    const nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));

    if (nights <= 0) {
        document.getElementById('total-display').textContent = 'Tanggal tidak valid';
        return;
    }

    const total = nights * pricePerNight;
    const formatted = 'Rp ' + total.toLocaleString('id-ID');

    document.getElementById('nights-row').style.display = 'flex';
    document.getElementById('nights-display').textContent = nights + ' malam';
    document.getElementById('total-display').textContent = formatted;
    document.getElementById('total-note').textContent = nights + ' malam × Rp ' + pricePerNight.toLocaleString('id-ID');

    // Set check_out min to day after check_in
    document.getElementById('check_out').min = checkIn;
}

// Run on load if values already set (e.g. after validation error)
calculateTotal();
</script>
</body>
</html>