<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $province->name }} — NusantaraGuide</title>
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
        .nav-links a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--lime); }
        .nav-user { display: flex; align-items: center; gap: 12px; }
        .nav-user span { color: rgba(255,255,255,0.7); font-size: 14px; }
        .logout-btn { background: transparent; border: 1px solid rgba(255,255,255,0.3); color: rgba(255,255,255,0.7); padding: 7px 16px; border-radius: 6px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 13px; }

        /* HERO */
        .page-hero {
            background: linear-gradient(160deg, rgba(13,27,19,0.78) 0%, rgba(26,58,42,0.62) 100%),
                        url('https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1400&q=80') center/cover no-repeat;
            padding: 64px 8% 52px;
        }
        .breadcrumb {
            font-size: 13px; color: rgba(255,255,255,0.6);
            display: flex; align-items: center; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;
        }
        .breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; }
        .breadcrumb a:hover { color: var(--lime); }
        .page-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px,5vw,56px); color: white; font-weight: 900; margin-bottom: 12px;
        }
        .page-hero p { font-size: 16px; color: rgba(255,255,255,0.75); max-width: 560px; line-height: 1.7; }

        /* REGIONS */
        .regions-section { padding: 48px 8% 0; }
        .section-label {
            font-size: 12px; font-weight: 700; letter-spacing: 2px;
            text-transform: uppercase; color: var(--muted); margin-bottom: 16px;
        }
        .regions-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 48px; }
        .region-chip {
            padding: 9px 20px; border-radius: 24px; border: 1.5px solid #e0dbd2;
            font-size: 14px; font-weight: 500; color: var(--ink);
            text-decoration: none; background: white; transition: all 0.2s;
            display: flex; align-items: center; gap: 8px;
        }
        .region-chip:hover { background: var(--jade); color: white; border-color: var(--jade); }
        .region-chip .chip-count {
            background: var(--cream); color: var(--muted);
            font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: 600;
        }
        .region-chip:hover .chip-count { background: rgba(255,255,255,0.2); color: white; }

        /* PLACES */
        .places-section { padding: 0 8% 60px; }
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px; font-weight: 700; color: var(--forest);
        }
        .see-all-link {
            font-size: 14px; font-weight: 600; color: var(--jade);
            text-decoration: none; display: flex; align-items: center; gap: 4px; transition: gap 0.2s;
        }
        .see-all-link:hover { gap: 8px; }

        .place-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 22px;
        }
        .place-card {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: var(--card-shadow); transition: all 0.3s; display: flex; flex-direction: column;
        }
        .place-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(26,58,42,0.15); }
        .place-card-img { position: relative; }
        .place-card-img img { width: 100%; height: 190px; object-fit: cover; display: block; }
        .place-cat-badge {
            position: absolute; top: 12px; left: 12px;
            background: rgba(255,255,255,0.92); backdrop-filter: blur(4px);
            padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: var(--forest);
        }
        .place-card-body { padding: 18px 20px; flex: 1; display: flex; flex-direction: column; }
        .place-region-tag { font-size: 12px; color: var(--muted); margin-bottom: 5px; }
        .place-card-body h3 { font-size: 16px; font-weight: 600; color: var(--ink); margin-bottom: 5px; line-height: 1.3; }
        .place-card-body p { font-size: 13px; color: var(--muted); line-height: 1.6; flex: 1; }
        .place-card-footer {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 14px; padding-top: 12px; border-top: 1px solid #f5f5f5;
        }
        .place-price { font-size: 14px; font-weight: 700; color: var(--forest); }
        .btn-detail {
            font-size: 13px; font-weight: 600; color: white;
            background: var(--jade); padding: 7px 16px; border-radius: 8px;
            text-decoration: none; transition: background 0.2s;
        }
        .btn-detail:hover { background: var(--forest); }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 20px; grid-column: 1/-1; }
        .empty-state .icon { font-size: 48px; margin-bottom: 14px; }
        .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 20px; color: var(--ink); margin-bottom: 6px; }
        .empty-state p { font-size: 14px; color: var(--muted); }
        .empty-state a { color: var(--jade); font-weight: 600; text-decoration: none; }

        footer { background: var(--ink); color: rgba(255,255,255,0.5); padding: 32px 8%; text-align: center; font-size: 14px; }
        footer strong { color: var(--lime); }

        @media (max-width: 768px) {
            .regions-section, .places-section { padding-left: 5%; padding-right: 5%; }
            .place-grid { grid-template-columns: 1fr; }
            .page-hero { padding: 48px 5% 40px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="logo">Nusantara<span>Guide</span></a>
    <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('explore') }}" class="active">Explore</a></li>
        <li><a href="{{ route('places.browse') }}">Tempat</a></li>
        <li><a href="{{ route('itinerary.index') }}">Itinerary</a></li>
    </ul>
    <div class="nav-user">
        @auth
            <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf <button type="submit" class="logout-btn">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="logout-btn" style="text-decoration:none;display:inline-block;">Login</a>
        @endauth
    </div>
</nav>

<!-- HERO -->
<div class="page-hero">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a> /
        <a href="{{ route('explore') }}">Explore</a> /
        <span style="color:rgba(255,255,255,0.9);">{{ $province->name }}</span>
    </div>
    <h1>{{ $province->name }}</h1>
    <p>{{ $province->description ?? 'Temukan destinasi wisata terbaik di ' . $province->name . '.' }}</p>
</div>

<!-- REGIONS -->
<div class="regions-section">
    <div class="section-label">Wilayah di {{ $province->name }}</div>
    <div class="regions-row">
        {{-- "All" chip --}}
        <a href="{{ route('province.show', $province->slug) }}"
           class="region-chip" style="{{ !request('region') ? 'background:var(--forest);color:white;border-color:var(--forest);' : '' }}">
            Semua Wilayah
            <span class="chip-count">{{ $places->count() }}</span>
        </a>
        @foreach($regions as $region)
            <a href="{{ route('region.show', [$province->slug, $region->slug ?? $region->id]) }}"
               class="region-chip">
                {{ $region->name }}
                <span class="chip-count">{{ $region->places_count }}</span>
            </a>
        @endforeach
    </div>
</div>

<!-- PLACES -->
<div class="places-section">
    <div class="section-header">
        <div class="section-title">Tempat Wisata di {{ $province->name }}</div>
        <a href="{{ route('places.browse', ['province' => $province->id]) }}" class="see-all-link">
            Lihat Semua →
        </a>
    </div>

    <div class="place-grid">
        @forelse($places as $place)
        <div class="place-card">
            <div class="place-card-img">
                <img src="{{ asset($place->image_url ?? 'images/default.jpg') }}"
                     alt="{{ $place->name }}"
                     onerror="this.style.height='190px';this.style.background='#e8f4ee';this.style.display='none';">
                @if($place->category)
                    <span class="place-cat-badge">{{ $place->category->icon ?? '' }} {{ $place->category->name }}</span>
                @endif
            </div>
            <div class="place-card-body">
                <div class="place-region-tag">📍 {{ $place->region->name ?? '' }}</div>
                <h3>{{ $place->name }}</h3>
                <p>{{ Str::limit($place->description, 85) }}</p>
                <div class="place-card-footer">
                    <span class="place-price">
                        @if($place->price_start == 0) Gratis
                        @else Rp {{ number_format($place->price_start, 0, ',', '.') }}
                        @endif
                    </span>
                    <a href="{{ route('place.show', $place->id) }}" class="btn-detail">Lihat Detail →</a>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="icon">🗺️</div>
            <h3>Belum ada tempat</h3>
            <p>Belum ada destinasi yang terdaftar di {{ $province->name }}.</p>
        </div>
        @endforelse
    </div>
</div>

<footer>
    <p>© {{ date('Y') }} NusantaraGuide — Panduan wisata seluruh <strong>Indonesia</strong></p>
</footer>

</body>
</html>
