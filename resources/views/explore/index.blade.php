<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Indonesia — NusantaraGuide</title>
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
            background: linear-gradient(135deg, var(--forest) 0%, #2d5a3d 100%);
            padding: 60px 8% 50px; text-align: center;
        }
        .page-hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(32px,5vw,56px); color: white; font-weight: 900; margin-bottom: 12px; }
        .page-hero p { font-size: 17px; color: rgba(255,255,255,0.72); max-width: 520px; margin: 0 auto 28px; line-height: 1.7; }
        .island-stats {
            display: flex; justify-content: center; gap: 48px; flex-wrap: wrap;
        }
        .island-stat { text-align: center; }
        .island-stat .num { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--gold); font-weight: 900; }
        .island-stat .lbl { font-size: 12px; color: rgba(255,255,255,0.55); letter-spacing: 1px; text-transform: uppercase; margin-top: 4px; }

        /* PROVINCE GRID */
        .section { padding: 56px 8%; }
        .section-tag { font-size: 12px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--jade); display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
        .section-tag::before { content: ''; width: 24px; height: 2px; background: var(--jade); }
        .section-title { font-family: 'Playfair Display', serif; font-size: clamp(24px,4vw,36px); font-weight: 700; color: var(--forest); margin-bottom: 36px; }

        .province-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 22px;
        }
        .province-card {
            border-radius: 18px; overflow: hidden; position: relative;
            height: 220px; text-decoration: none; display: block;
            transition: transform 0.3s, box-shadow 0.3s;
            background: var(--forest);
        }
        .province-card:hover { transform: translateY(-7px); box-shadow: 0 20px 48px rgba(0,0,0,0.22); }
        .province-card img {
            width: 100%; height: 100%; object-fit: cover; opacity: 0.75;
            transition: transform 0.4s, opacity 0.3s;
        }
        .province-card:hover img { transform: scale(1.06); opacity: 0.65; }
        .province-card-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(13,27,19,0.88) 0%, rgba(0,0,0,0.05) 55%);
            padding: 20px; display: flex; flex-direction: column; justify-content: flex-end;
        }
        .province-name { font-family: 'Playfair Display', serif; font-size: 21px; color: white; font-weight: 700; line-height: 1.2; }
        .province-meta { display: flex; align-items: center; gap: 12px; margin-top: 6px; }
        .province-count { font-size: 13px; color: rgba(255,255,255,0.65); }
        .province-arrow {
            margin-left: auto; background: rgba(255,255,255,0.15);
            border-radius: 50%; width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; color: white; transition: background 0.2s;
        }
        .province-card:hover .province-arrow { background: var(--coral); }

        /* ISLAND GROUPS */
        .island-section { padding: 0 8% 56px; }
        .island-title {
            font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
            color: var(--muted); margin-bottom: 16px; padding-bottom: 10px;
            border-bottom: 1px solid #e8e3da;
        }
        .island-group { margin-bottom: 48px; }

        footer { background: var(--ink); color: rgba(255,255,255,0.5); padding: 32px 8%; text-align: center; font-size: 14px; }
        footer strong { color: var(--lime); }

        @media (max-width: 768px) {
            .section { padding: 40px 5%; }
            .island-section { padding: 0 5% 40px; }
            .province-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
            .province-card { height: 160px; }
            .province-name { font-size: 16px; }
            .island-stats { gap: 24px; }
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
    <h1>Explore Indonesia 🌏</h1>
    <p>Dari Sabang sampai Merauke — temukan keindahan di setiap sudut Nusantara.</p>
    <div class="island-stats">
        <div class="island-stat"><div class="num">34</div><div class="lbl">Provinsi</div></div>
        <div class="island-stat"><div class="num">17.504</div><div class="lbl">Pulau</div></div>
        <div class="island-stat"><div class="num">500+</div><div class="lbl">Destinasi</div></div>
    </div>
</div>

<!-- ALL PROVINCES -->
<div class="section">
    <div class="section-tag">Destinasi</div>
    <div class="section-title">Semua Provinsi</div>

    <div class="province-grid">
        @forelse($provinces as $province)
        <a href="{{ route('province.show', $province->slug) }}" class="province-card">
            {{-- Fallback gradient backgrounds per province if no image --}}
            @if($province->image_url)
                <img src="{{ asset($province->image_url) }}" alt="{{ $province->name }}">
            @else
                {{-- Show a placeholder pattern instead --}}
                <div style="width:100%;height:100%;background:linear-gradient(135deg,
                    {{ ['#1a3a2a','#2d4a3a','#1e4a34','#2a3d2a','#163020','#243828','#1c3a24','#2d3e30','#1a2e22','#263a2c'][$loop->index % 10] }},
                    {{ ['#2d6a4f','#3d7a5f','#257a4f','#3a6a4f','#265a3f','#346054','#2a6044','#3a5a44','#2a5a3a','#336048'][$loop->index % 10] }}
                    );">
                </div>
            @endif
            <div class="province-card-overlay">
                <div class="province-name">{{ $province->name }}</div>
                <div class="province-meta">
                    <span class="province-count">{{ $province->regions_count ?? 0 }} wilayah</span>
                    <span class="province-arrow">→</span>
                </div>
            </div>
        </a>
        @empty
        <p style="color:var(--muted); grid-column:1/-1;">Belum ada data provinsi.</p>
        @endforelse
    </div>
</div>

<footer>
    <p>© {{ date('Y') }} NusantaraGuide — Panduan wisata seluruh <strong>Indonesia</strong></p>
</footer>

</body>
</html>