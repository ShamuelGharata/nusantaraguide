<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NusantaraGuide')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --forest:#1a3a2a; --jade:#2d6a4f; --lime:#74c69d;
            --cream:#fdf8f0; --gold:#e9c46a; --coral:#e76f51;
            --ink:#0d1b13; --muted:#6b7c72;
            --card-shadow:0 4px 24px rgba(26,58,42,0.10);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DM Sans',sans-serif; background:var(--cream); color:var(--ink); }

        /* ── NAVBAR ── */
        .navbar {
            position:sticky; top:0; z-index:100;
            background:rgba(26,58,42,0.97); backdrop-filter:blur(12px);
            padding:0 6%; display:flex; align-items:center;
            justify-content:space-between; height:64px;
            border-bottom:1px solid rgba(116,198,157,0.15);
        }
        .logo {
            font-family:'Playfair Display',serif;
            font-size:20px; font-weight:900; color:white; text-decoration:none;
            flex-shrink:0;
        }
        .logo span { color:var(--gold); }

        .nav-links { display:flex; gap:4px; list-style:none; align-items:center; }
        .nav-links a {
            color:rgba(255,255,255,0.75); text-decoration:none;
            font-size:14px; font-weight:500; padding:7px 13px;
            border-radius:7px; transition:all 0.2s; white-space:nowrap;
        }
        .nav-links a:hover { color:white; background:rgba(255,255,255,0.08); }
        .nav-links a.active { color:var(--lime); background:rgba(116,198,157,0.1); }
        .nav-links a.nav-cta {
            background:var(--coral); color:white !important;
            padding:7px 16px; margin-left:4px;
        }
        .nav-links a.nav-cta:hover { background:#d4604a; }

        .nav-right { display:flex; align-items:center; gap:12px; flex-shrink:0; }
        .nav-user-name { color:rgba(255,255,255,0.75); font-size:14px; }
        .logout-btn {
            background:transparent; border:1px solid rgba(255,255,255,0.25);
            color:rgba(255,255,255,0.7); padding:7px 16px; border-radius:7px;
            cursor:pointer; font-family:'DM Sans',sans-serif; font-size:13px;
            transition:all 0.2s;
        }
        .logout-btn:hover { border-color:var(--coral); color:var(--coral); }
        .login-btn {
            color:rgba(255,255,255,0.75); text-decoration:none;
            font-size:14px; font-weight:500; padding:7px 16px;
            border:1px solid rgba(255,255,255,0.25); border-radius:7px;
            transition:all 0.2s;
        }
        .login-btn:hover { color:white; border-color:rgba(255,255,255,0.5); }
        .register-btn {
            background:var(--coral); color:white; text-decoration:none;
            font-size:14px; font-weight:600; padding:7px 16px; border-radius:7px;
            transition:background 0.2s;
        }
        .register-btn:hover { background:#d4604a; }

        /* ── PAGINATION (global) ── */
        .pagination-wrap nav > div:first-child{display:none;}
        .pagination-wrap{display:flex;justify-content:center;margin-top:32px;}
        .pagination-wrap ul,.pagination-wrap .flex{display:flex!important;justify-content:center;gap:6px;list-style:none;flex-wrap:wrap;align-items:center;}
        .pagination-wrap a,.pagination-wrap span,.pagination-wrap button{display:inline-flex!important;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;border:1.5px solid #e0dbd2!important;background:white;color:var(--ink);transition:all 0.2s;cursor:pointer;font-family:'DM Sans',sans-serif;}
        .pagination-wrap a:hover{border-color:var(--jade)!important;color:var(--jade);}
        .pagination-wrap [aria-current="page"] span{background:var(--jade)!important;color:white!important;border-color:var(--jade)!important;}
        .pagination-wrap span[aria-disabled="true"]{color:#ccc!important;border-color:#eee!important;cursor:default;}

    </style>
    @yield('styles')
    @yield('head')
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="logo">Nusantara<span>Guide</span></a>

    <ul class="nav-links">
        <li><a href="{{ route('home') }}"              {{ request()->routeIs('home')                ? 'class=active' : '' }}>Beranda</a></li>
        <li><a href="{{ route('explore') }}"           {{ request()->routeIs('explore','province.*','region.*') ? 'class=active' : '' }}>Eksplorasi</a></li>
        <li><a href="{{ route('places.browse') }}"     {{ request()->routeIs('places.*','place.*')  ? 'class=active' : '' }}>Tempat</a></li>
        <li><a href="{{ route('itinerary.index') }}"   {{ request()->routeIs('itinerary.*')         ? 'class=active' : '' }}>Itinerary</a></li>
        <li><a href="{{ route('transportation.index') }}" {{ request()->routeIs('transportation.*') ? 'class=active' : '' }}>Transportasi</a></li>
        @auth
        <li><a href="{{ route('bookings.my') }}"       {{ request()->routeIs('bookings.*','booking.*') ? 'class=active' : '' }}>Bookings</a></li>
        <li><a href="{{ route('itinerary.create') }}"  class="nav-cta">+ Buat Itinerary</a></li>
        @endauth
    </ul>

    <div class="nav-right">
        @auth
            <span class="nav-user-name">Halo, {{ explode(' ', Auth::user()->name)[0] }}!</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="login-btn">Masuk</a>
            <a href="{{ route('register') }}" class="register-btn">Daftar</a>
        @endauth
    </div>
</nav>

@yield('content')

<footer style="background:var(--ink);color:rgba(255,255,255,0.5);padding:32px 8%;text-align:center;font-size:14px;margin-top:0;">
    <p style="font-family:'Playfair Display',serif;font-size:18px;color:white;margin-bottom:6px;">NusantaraGuide</p>
    <p>© {{ date('Y') }} NusantaraGuide — Panduan wisata seluruh <strong style="color:var(--lime);">Indonesia</strong></p>
    <p style="margin-top:8px;font-size:12px;">Dibuat dengan ❤️ untuk para penjelajah Nusantara</p>
</footer>

@yield('scripts')
</body>
</html>