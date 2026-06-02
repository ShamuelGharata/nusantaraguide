@extends('layouts.app')

@section('title', 'Itinerary — NusantaraGuide')

@section('content')
<!-- HERO -->
<div class="hero">
    @if($itinerary->cover_image)
        <img src="{{ asset('storage/'.$itinerary->cover_image) }}" alt="{{ $itinerary->title }}">
    @endif
    <div class="hero-overlay">
        <div class="hero-badges">
            <span class="badge badge-style">{{ $itinerary->travel_style_label }}</span>
            @if($itinerary->is_verified)
                <span class="badge badge-verified">✓ Perjalanan Nyata</span>
            @endif
            @if($itinerary->region)
                <span class="badge badge-region">📍 {{ $itinerary->region->name }}, {{ $itinerary->region->province->name ?? '' }}</span>
            @endif
        </div>
        <div class="hero-title">{{ $itinerary->title }}</div>
        <div class="hero-meta">
            <div class="hero-author">
                <div class="author-avatar">{{ strtoupper(substr($itinerary->user->name, 0, 1)) }}</div>
                <div>
                    <div class="author-name">{{ $itinerary->user->name }}</div>
                    <div class="author-date">{{ \Carbon\Carbon::parse($itinerary->created_at)->isoFormat('D MMMM YYYY') }}</div>
                </div>
            </div>
            <div class="hero-stats">
                <span class="hero-stat">👁 {{ $itinerary->views }}</span>
                <span class="hero-stat">❤️ {{ $itinerary->likes->count() }}</span>
                <span class="hero-stat">💬 {{ $itinerary->comments->count() }}</span>
                <span class="hero-stat">📋 {{ $itinerary->copies->count() }} disalin</span>
            </div>
        </div>
    </div>
</div>

<!-- BODY -->
<div class="page-body">

    <!-- LEFT COLUMN -->
    <div>

        <!-- OVERVIEW -->
        <div class="section">
            <h2>Ringkasan Perjalanan</h2>
            <div class="overview-grid">
                <div class="overview-item">
                    <div class="oi-icon">📅</div>
                    <div class="oi-val">{{ $itinerary->duration_days }} Hari</div>
                    <div class="oi-label">Durasi</div>
                </div>
                <div class="overview-item">
                    <div class="oi-icon">💰</div>
                    <div class="oi-val">{{ $itinerary->budget_label }}</div>
                    <div class="oi-label">{{ $itinerary->actual_budget ? 'Budget Aktual' : 'Estimasi' }}</div>
                </div>
                @if($itinerary->trip_rating)
                <div class="overview-item">
                    <div class="oi-icon">⭐</div>
                    <div class="oi-val">{{ $itinerary->trip_rating }}/5</div>
                    <div class="oi-label">Rating</div>
                </div>
                @endif
                <div class="overview-item">
                    <div class="oi-icon">👥</div>
                    <div class="oi-val">{{ ucfirst($itinerary->travel_style) }}</div>
                    <div class="oi-label">Gaya</div>
                </div>
                @if($totalCost > 0)
                <div class="overview-item">
                    <div class="oi-icon">🧾</div>
                    <div class="oi-val" style="font-size:13px;">Rp {{ number_format($totalCost/1000000, 1) }}Jt</div>
                    <div class="oi-label">Total Stop</div>
                </div>
                @endif
            </div>
            @if($itinerary->summary)
                <p style="margin-top:18px;font-size:15px;color:#444;line-height:1.8;">{{ $itinerary->summary }}</p>
            @endif
            @if($itinerary->tags->count())
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;">
                    @foreach($itinerary->tags as $tag)
                        <span style="background:rgba(45,106,79,0.08);color:var(--jade);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- DAY BY DAY TIMELINE -->
        @foreach($itinerary->days as $day)
        <div class="section">
            <div class="day-heading">
                <div class="day-num-badge">{{ $day->day_number }}</div>
                <div class="day-title-text">{{ $day->title ?? 'Hari ' . $day->day_number }}</div>
            </div>

            @if($day->notes)
                <p class="day-notes-text">{{ $day->notes }}</p>
            @endif

            @if($day->stops->count())
            <div class="timeline">
                @foreach($day->stops as $stop)
                <div class="stop">
                    <div class="stop-dot">{{ $stop->icon }}</div>
                    <div class="stop-card {{ $stop->stop_type }}-card">
                        <div class="stop-header-row">
                            <span class="stop-name">{{ $stop->display_name }}</span>
                            <div style="display:flex;align-items:center;gap:6px;">
                                @if($stop->visit_time)
                                    <span class="stop-time">🕐 {{ substr($stop->visit_time, 0, 5) }}</span>
                                @endif
                                @if($stop->flight_booking_id || $stop->hotel_booking_id)
                                    <span class="verified-chip">✓ Booking Nyata</span>
                                @endif
                            </div>
                        </div>

                        {{-- Flight details --}}
                        @if($stop->stop_type === 'flight' && $stop->flightBooking)
                            @php $fb = $stop->flightBooking; $fl = $fb->flight; @endphp
                            <div class="stop-detail">
                                {{ $fl->airline->name }} {{ $fl->flight_number }} ·
                                {{ substr($fl->departure_time,0,5) }} – {{ substr($fl->arrival_time,0,5) }} ·
                                {{ $fl->duration_label }}
                            </div>
                            <div class="stop-detail">{{ $fb->seat_class === 'business' ? '💼 Bisnis' : '🪑 Ekonomi' }} · {{ $fb->passengers }} penumpang</div>
                        @elseif($stop->stop_type === 'flight' && $stop->custom_title)
                            <div class="stop-detail">{{ $stop->custom_title }}</div>

                        {{-- Hotel details --}}
                        @elseif($stop->stop_type === 'hotel' && $stop->hotelBooking)
                            @php $hb = $stop->hotelBooking; @endphp
                            <div class="stop-detail">
                                🛏️ {{ $hb->room->name }} ·
                                {{ $hb->check_in->isoFormat('D MMM') }} – {{ $hb->check_out->isoFormat('D MMM Y') }} ·
                                {{ $hb->nights }} malam
                            </div>
                            <a href="{{ route('place.show', $hb->place_id) }}" class="stop-link">Lihat hotel →</a>
                        @elseif($stop->stop_type === 'hotel' && $stop->custom_title)
                            <div class="stop-detail">{{ $stop->custom_title }}</div>

                        {{-- Place details --}}
                        @elseif($stop->stop_type === 'place')
                            @if($stop->place)
                                <div class="stop-detail">{{ Str::limit($stop->place->description, 100) }}</div>
                                <a href="{{ route('place.show', $stop->place_id) }}" class="stop-link">Lihat tempat →</a>
                            @elseif($stop->custom_place_name)
                                <div class="stop-detail">{{ $stop->custom_place_name }}</div>
                            @endif
                            @if($stop->duration_minutes)
                                <div class="stop-detail">⏱ {{ $stop->duration_minutes }} menit</div>
                            @endif

                        {{-- Custom --}}
                        @elseif($stop->stop_type === 'custom')
                            @if($stop->custom_description)
                                <div class="stop-detail">{{ $stop->custom_description }}</div>
                            @endif
                        @endif

                        @if($stop->tip)
                            <div class="stop-tip">💡 {{ $stop->tip }}</div>
                        @endif
                        @if($stop->display_cost)
                            <div class="stop-cost">Rp {{ number_format($stop->display_cost, 0, ',', '.') }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
                <p style="color:var(--muted);font-size:14px;">Tidak ada aktivitas tercatat untuk hari ini.</p>
            @endif
        </div>
        @endforeach

        <!-- FULL STORY -->
        @if($itinerary->body)
        <div class="section">
            <h2>Cerita Perjalanan</h2>
            <div class="story-body">{{ $itinerary->body }}</div>
        </div>
        @endif

        <!-- TIPS -->
        @if($itinerary->trip_tips)
        <div class="section">
            <h2>Tips dari {{ $itinerary->user->name }}</h2>
            <div class="tips-box">
                <h3>💡 Saran untuk kamu yang ingin mencoba</h3>
                <p>{{ $itinerary->trip_tips }}</p>
            </div>
        </div>
        @endif

        <!-- COMMENTS -->
        <div class="section">
            <h2>Komentar ({{ $itinerary->comments->count() }})</h2>

            @if($itinerary->comments->count())
                @foreach($itinerary->comments as $comment)
                <div class="comment-item">
                    <div class="comment-author">
                        <div class="comment-avatar">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
                        <div>
                            <div class="comment-name">{{ $comment->user->name }}</div>
                            <div class="comment-date">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="comment-text">{{ $comment->comment }}</div>
                </div>
                @endforeach
            @else
                <p style="color:var(--muted);font-size:14px;margin-bottom:20px;">Belum ada komentar. Jadilah yang pertama!</p>
            @endif

            @auth
            <div class="comment-form" style="margin-top:20px;">
                <form action="{{ route('itinerary.comment', $itinerary->id) }}" method="POST">
                    @csrf
                    <textarea name="comment" placeholder="Tulis komentarmu..." required></textarea>
                    <button type="submit" class="comment-submit">Kirim Komentar</button>
                </form>
            </div>
            @else
            <p style="margin-top:16px;font-size:14px;color:var(--muted);">
                <a href="{{ route('login') }}" style="color:var(--jade);font-weight:600;">Login</a> untuk berkomentar.
            </p>
            @endauth
        </div>

    </div>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <!-- COPY / LIKE / SAVE -->
        <div class="sidebar-card">
            @auth
                {{-- Copy button --}}
                @if($itinerary->user_id !== Auth::id())
                    @if(!$isCopied)
                        <form action="{{ route('itinerary.copy', $itinerary->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="action-btn btn-copy">📋 Salin Itinerary Ini</button>
                        </form>
                    @else
                        <div class="action-btn btn-copied">✓ Sudah Disalin</div>
                    @endif
                @endif

                {{-- Like --}}
                <form action="{{ route('itinerary.like', $itinerary->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="action-btn btn-like {{ $isLiked ? 'liked' : '' }}" style="border:2px solid var(--coral);">
                        {{ $isLiked ? '❤️ Disukai' : '🤍 Suka' }} ({{ $itinerary->likes->count() }})
                    </button>
                </form>

                {{-- Save --}}
                <form action="{{ route('itinerary.save', $itinerary->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="action-btn btn-save {{ $isSaved ? 'saved' : '' }}" style="border:2px solid var(--jade);">
                        {{ $isSaved ? '🔖 Tersimpan' : '🔖 Simpan' }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="action-btn btn-copy">Login untuk Menyalin</a>
            @endauth
        </div>

        <!-- BUDGET BREAKDOWN -->
        @if($totalCost > 0 || $itinerary->actual_budget)
        <div class="sidebar-card">
            <h3>💰 Rincian Biaya</h3>
            @foreach($itinerary->days as $day)
                @foreach($day->stops as $stop)
                    @if($stop->display_cost)
                    <div class="budget-row">
                        <span style="font-size:13px;color:var(--muted);">{{ Str::limit($stop->display_name, 28) }}</span>
                        <span style="font-size:13px;font-weight:600;">Rp {{ number_format($stop->display_cost/1000, 0) }}K</span>
                    </div>
                    @endif
                @endforeach
            @endforeach
            @if($totalCost > 0)
            <div class="budget-total">
                <span>Total</span>
                <span>Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($itinerary->actual_budget && $itinerary->actual_budget != $totalCost)
            <div style="font-size:12px;color:var(--muted);margin-top:8px;text-align:center;">
                Budget aktual yang dilaporkan: <strong>{{ $itinerary->budget_label }}</strong>
            </div>
            @endif
        </div>
        @endif

        <!-- WHAT'S BOOKABLE -->
        @php
            $bookableStops = $itinerary->days->flatMap(fn($d) => $d->stops)->filter(fn($s) => $s->stop_type === 'flight' || $s->stop_type === 'hotel');
        @endphp
        @if($bookableStops->count())
        <div class="sidebar-card">
            <h3>🔗 Bisa Dipesan di Sini</h3>
            <p style="font-size:13px;color:var(--muted);margin-bottom:14px;">Salin itinerary ini, lalu pesan langsung:</p>
            @foreach($bookableStops as $stop)
            <div style="padding:10px 0;border-bottom:1px solid #f5f5f5;">
                <div style="font-size:13px;font-weight:600;color:var(--ink);">{{ $stop->display_name }}</div>
                @if($stop->stop_type === 'flight')
                    <a href="{{ route('transportation.index', ['type'=>'flight']) }}" style="font-size:12px;color:var(--jade);text-decoration:none;font-weight:600;">Cari penerbangan serupa →</a>
                @elseif($stop->stop_type === 'hotel' && $stop->hotelBooking)
                    <a href="{{ route('place.show', $stop->hotelBooking->place_id) }}" style="font-size:12px;color:var(--jade);text-decoration:none;font-weight:600;">Lihat hotel →</a>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- RELATED -->
        @if($related->count())
        <div class="sidebar-card">
            <h3>Itinerary Serupa</h3>
            @foreach($related as $r)
            <a href="{{ route('itinerary.show', $r->slug) }}" class="related-card">
                <div class="related-thumb">🗺️</div>
                <div>
                    <div class="related-title">{{ Str::limit($r->title, 45) }}</div>
                    <div class="related-meta">{{ $r->duration_days }} hari · ❤️ {{ $r->likes_count }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endsection