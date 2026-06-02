@extends('layouts.app')

@section('title', 'Buat Itinerary — NusantaraGuide')

@section('styles')
<style>
    .page-wrap{max-width:900px;margin:40px auto;padding:0 24px 80px;}
    .page-title{font-family:'Playfair Display',serif;font-size:32px;font-weight:900;color:var(--forest);margin-bottom:8px;}
    .page-sub{font-size:15px;color:var(--muted);margin-bottom:32px;}
    .form-card{background:white;border-radius:18px;padding:28px;box-shadow:var(--card-shadow);margin-bottom:24px;}
    .card-title{font-size:16px;font-weight:700;color:var(--forest);margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--cream);display:flex;align-items:center;gap:8px;}
    .form-group{margin-bottom:18px;}
    .form-group label{display:block;font-size:12px;font-weight:700;color:var(--muted);letter-spacing:0.5px;text-transform:uppercase;margin-bottom:6px;}
    .form-group input,.form-group select,.form-group textarea{width:100%;padding:11px 14px;border:1.5px solid #e0dbd2;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--ink);background:var(--cream);outline:none;transition:border-color 0.2s;}
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--jade);background:white;}
    .form-group textarea{resize:vertical;min-height:80px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
    .budget-tracker{background:linear-gradient(135deg,var(--forest),var(--jade));color:white;border-radius:14px;padding:20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;}
    .budget-tracker .label{font-size:12px;opacity:0.75;text-transform:uppercase;letter-spacing:0.5px;}
    .budget-tracker .amount{font-family:'Playfair Display',serif;font-size:28px;font-weight:900;}
    .budget-tracker .note{font-size:12px;opacity:0.6;margin-top:2px;}
    .day-block{background:white;border-radius:16px;box-shadow:var(--card-shadow);margin-bottom:20px;overflow:hidden;}
    .day-header{background:var(--forest);color:white;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;}
    .day-header-left{display:flex;align-items:center;gap:12px;}
    .day-number{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;}
    .day-title-input{background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:8px;padding:6px 12px;color:white;font-family:'DM Sans',sans-serif;font-size:13px;outline:none;width:220px;}
    .day-title-input::placeholder{color:rgba(255,255,255,0.5);}
    .remove-day-btn{background:rgba(255,255,255,0.15);border:none;color:white;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;font-family:'DM Sans',sans-serif;}
    .remove-day-btn:hover{background:var(--coral);}
    .day-body{padding:20px;}
    .day-notes-input{width:100%;padding:10px 14px;border:1.5px solid #e0dbd2;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--muted);background:var(--cream);outline:none;margin-bottom:16px;resize:none;}
    .day-notes-input:focus{border-color:var(--jade);background:white;}
    .stops-list{display:flex;flex-direction:column;gap:12px;margin-bottom:16px;}
    .stop-item{border:1.5px solid #e8e3da;border-radius:12px;overflow:hidden;}
    .stop-header{display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fafaf8;border-bottom:1px solid #f0ede8;}
    .stop-type-select{padding:6px 10px;border:1px solid #e0dbd2;border-radius:6px;font-family:'DM Sans',sans-serif;font-size:13px;background:white;cursor:pointer;}
    .stop-time-input{padding:6px 10px;border:1px solid #e0dbd2;border-radius:6px;font-size:13px;width:100px;}
    .remove-stop-btn{margin-left:auto;background:none;border:none;color:var(--muted);cursor:pointer;font-size:16px;padding:4px;}
    .remove-stop-btn:hover{color:var(--coral);}
    .stop-body{padding:12px 14px;display:flex;flex-direction:column;gap:10px;}
    .stop-field{width:100%;padding:9px 12px;border:1.5px solid #e0dbd2;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:13px;background:var(--cream);outline:none;}
    .stop-field:focus{border-color:var(--jade);background:white;}
    .stop-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .stop-cost-input{padding:9px 12px;border:1.5px solid #e0dbd2;border-radius:8px;font-size:13px;background:var(--cream);outline:none;width:100%;}
    .add-stop-row{display:flex;gap:8px;flex-wrap:wrap;}
    .add-stop-btn{display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;border:1.5px dashed #d0c9c0;background:white;color:var(--muted);font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;}
    .add-stop-btn:hover{border-color:var(--jade);color:var(--jade);background:rgba(45,106,79,0.04);}
    .add-day-btn{width:100%;padding:14px;border:2px dashed #d0c9c0;border-radius:14px;background:white;color:var(--muted);font-size:14px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:8px;}
    .add-day-btn:hover{border-color:var(--jade);color:var(--jade);}
    .submit-row{display:flex;gap:12px;}
    .btn-publish{flex:1;padding:15px;background:var(--coral);color:white;border:none;border-radius:12px;font-family:'DM Sans',sans-serif;font-size:15px;font-weight:700;cursor:pointer;transition:all 0.2s;}
    .btn-publish:hover{background:#d4604a;}
    .btn-draft{padding:15px 28px;background:white;color:var(--muted);border:1.5px solid #e0dbd2;border-radius:12px;font-family:'DM Sans',sans-serif;font-size:15px;font-weight:600;cursor:pointer;transition:all 0.2s;}
    .btn-draft:hover{border-color:var(--jade);color:var(--jade);}
    .tags-grid{display:flex;flex-wrap:wrap;gap:8px;}
    .tag-cb{display:none;}
    .tag-label{padding:7px 16px;border-radius:20px;border:1.5px solid #e0dbd2;font-size:13px;font-weight:500;color:var(--muted);cursor:pointer;transition:all 0.2s;}
    .tag-cb:checked + .tag-label{background:var(--forest);color:white;border-color:var(--forest);}
    @media(max-width:700px){.form-row,.form-row-3{grid-template-columns:1fr;}.submit-row{flex-direction:column;}}
</style>
@endsection

@section('content')
<div class="page-wrap">
    <div class="page-title">✍️ Buat Itinerary</div>
    <div class="page-sub">Bagikan perjalananmu kepada komunitas NusantaraGuide.</div>

    <form action="{{ route('itinerary.store') }}" method="POST" enctype="multipart/form-data" id="itineraryForm">
        @csrf

        <!-- BASIC INFO -->
        <div class="form-card">
            <div class="card-title">📋 Informasi Dasar</div>
            <div class="form-group">
                <label>Judul Itinerary *</label>
                <input type="text" name="title" required placeholder="e.g. 5 Hari di Bali dengan Budget 3 Juta" value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label>Ringkasan *</label>
                <textarea name="summary" required placeholder="Ceritakan secara singkat perjalananmu (maks. 500 karakter)" maxlength="500">{{ old('summary') }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Destinasi Utama</label>
                    <select name="region_id">
                        <option value="">Pilih wilayah</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }} ({{ $region->province->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Durasi (hari) *</label>
                    <input type="number" name="duration_days" min="1" max="30" required value="{{ old('duration_days', 3) }}" id="durationInput" onchange="updateDays()">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Gaya Perjalanan *</label>
                    <select name="travel_style">
                        <option value="backpacker">🎒 Backpacker</option>
                        <option value="budget">💰 Budget</option>
                        <option value="mid-range" selected>🏡 Mid-range</option>
                        <option value="luxury">💎 Luxury</option>
                        <option value="family">👨‍👩‍👧 Keluarga</option>
                        <option value="solo">🧍 Solo</option>
                        <option value="couple">👫 Couple</option>
                        <option value="group">👥 Grup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Rating Perjalanan</label>
                    <select name="trip_rating">
                        <option value="">Tidak diisi</option>
                        <option value="5">⭐⭐⭐⭐⭐ Luar biasa</option>
                        <option value="4">⭐⭐⭐⭐ Sangat bagus</option>
                        <option value="3">⭐⭐⭐ Bagus</option>
                        <option value="2">⭐⭐ Biasa</option>
                        <option value="1">⭐ Kurang</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Estimasi Budget (Rp)</label>
                    <input type="number" name="estimated_budget" placeholder="Rencana awal" value="{{ old('estimated_budget') }}">
                </div>
                <div class="form-group">
                    <label>Budget Aktual (Rp)</label>
                    <input type="number" name="actual_budget" placeholder="Yang benar-benar dikeluarkan" value="{{ old('actual_budget') }}" id="actualBudget" onchange="updateBudgetDisplay()">
                </div>
            </div>
            <div class="form-group">
                <label>Tips & Saran</label>
                <textarea name="trip_tips" placeholder="Tips untuk traveler lain yang ingin mengikuti itinerary ini...">{{ old('trip_tips') }}</textarea>
            </div>
            <div class="form-group">
                <label>Cover Image</label>
                <input type="file" name="cover_image" accept="image/*">
            </div>
        </div>

        <!-- BUDGET TRACKER -->
        <div class="budget-tracker">
            <div>
                <div class="label">Total dari booking nyata</div>
                <div class="amount" id="autoTotal">Rp 0</div>
                <div class="note">Otomatis dari flight & hotel yang kamu link</div>
            </div>
            <div style="text-align:right;">
                <div class="label">Budget aktual (manual)</div>
                <div class="amount" id="manualBudgetDisplay">Belum diisi</div>
            </div>
        </div>

        <!-- DAY-BY-DAY BUILDER -->
        <div id="daysContainer"></div>

        <button type="button" class="add-day-btn" onclick="addDay()" id="addDayBtn">
            + Tambah Hari
        </button>

        <!-- CERITA PERJALANAN -->
        <div class="form-card" style="margin-top:24px;">
            <div class="card-title">📖 Cerita Perjalanan</div>
            <div class="form-group">
                <label>Tulis cerita lengkapmu (opsional)</label>
                <textarea name="body" style="min-height:200px;" placeholder="Ceritakan pengalamanmu secara lebih detail — apa yang berkesan, tantangan yang dihadapi, momen-momen spesial...">{{ old('body') }}</textarea>
            </div>
        </div>

        <!-- TAGS -->
        <div class="form-card">
            <div class="card-title">🏷️ Tags</div>
            <div class="tags-grid">
                @foreach($tags as $tag)
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag{{ $tag->id }}" class="tag-cb">
                    <label for="tag{{ $tag->id }}" class="tag-label">{{ $tag->name }}</label>
                @endforeach
            </div>
        </div>

        <!-- SUBMIT -->
        <div class="submit-row">
            <button type="submit" class="btn-publish" onclick="setStatus('published')">🚀 Publikasikan</button>
            <button type="button" class="btn-draft" onclick="setStatus('draft'); document.getElementById('itineraryForm').submit();">💾 Simpan Draft</button>
        </div>
        <input type="hidden" name="status" id="statusInput" value="published">

    </form>
</div>

<script>
// ── DATA FROM LARAVEL ──────────────────────────────────────────
const flightBookings = {{ Js::from($flightBookingsJson) }};
const hotelBookings = {{ Js::from($hotelBookingsJson) }};
const places = {{ Js::from($placesJson) }};

let dayCount = 0;
let autoTotal = 0;

// ── INIT ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const dur = parseInt(document.getElementById('durationInput').value) || 3;
    for (let i = 0; i < dur; i++) addDay();
});

function updateDays() {
    const target = parseInt(document.getElementById('durationInput').value) || 1;
    const current = document.querySelectorAll('.day-block').length;
    if (target > current) {
        for (let i = current; i < target; i++) addDay();
    } else if (target < current) {
        const days = document.querySelectorAll('.day-block');
        for (let i = days.length - 1; i >= target; i--) days[i].remove();
        dayCount = target;
    }
}

// ── ADD DAY ───────────────────────────────────────────────────
function addDay() {
    const idx = dayCount;
    dayCount++;
    const container = document.getElementById('daysContainer');
    const div = document.createElement('div');
    div.className = 'day-block';
    div.id = 'day-' + idx;
    div.innerHTML = `
        <div class="day-header">
            <div class="day-header-left">
                <span class="day-number">Hari ${dayCount}</span>
                <input type="hidden" name="days[${idx}][number]" value="${dayCount}">
                <input class="day-title-input" type="text" name="days[${idx}][title]" placeholder="Judul hari ini (opsional)">
            </div>
            <button type="button" class="remove-day-btn" onclick="removeDay('day-${idx}')">✕ Hapus Hari</button>
        </div>
        <div class="day-body">
            <textarea class="day-notes-input" name="days[${idx}][notes]" placeholder="Catatan umum untuk hari ini..." rows="2"></textarea>
            <div class="stops-list" id="stops-${idx}"></div>
            <div class="add-stop-row">
                <button type="button" class="add-stop-btn" onclick="addStop(${idx},'flight')">✈️ Tambah Penerbangan</button>
                <button type="button" class="add-stop-btn" onclick="addStop(${idx},'hotel')">🏨 Tambah Hotel</button>
                <button type="button" class="add-stop-btn" onclick="addStop(${idx},'place')">📍 Tambah Tempat</button>
                <button type="button" class="add-stop-btn" onclick="addStop(${idx},'custom')">📝 Tambah Aktivitas</button>
            </div>
        </div>`;
    container.appendChild(div);
}

function removeDay(id) {
    document.getElementById(id)?.remove();
}

// ── ADD STOP ──────────────────────────────────────────────────
let stopCount = 0;

function addStop(dayIdx, type) {
    const sIdx = stopCount++;
    const list = document.getElementById('stops-' + dayIdx);
    const div = document.createElement('div');
    div.className = 'stop-item';
    div.id = 'stop-' + sIdx;

    const typeIcons = {flight:'✈️',hotel:'🏨',place:'📍',custom:'📝'};
    const typeLabels = {flight:'Penerbangan',hotel:'Hotel',place:'Tempat Wisata',custom:'Aktivitas'};

    let bodyHTML = '';

    if (type === 'flight') {
        const opts = flightBookings.length
            ? flightBookings.map(b => `<option value="${b.id}" data-price="${b.price}">${b.label}</option>`).join('')
            : '<option value="">Belum ada booking penerbangan</option>';
        bodyHTML = `
            <select class="stop-field" name="days[${dayIdx}][stops][${sIdx}][flight_booking_id]" onchange="updateAutoTotal()">
                <option value="">Pilih booking penerbangan...</option>${opts}
            </select>
            <div class="stop-row">
                <input class="stop-field" type="text" name="days[${dayIdx}][stops][${sIdx}][custom_title]" placeholder="Atau ketik manual: e.g. Garuda GA300">
                <input class="stop-cost-input" type="number" name="days[${dayIdx}][stops][${sIdx}][cost]" placeholder="Biaya (Rp)" onchange="updateAutoTotal()">
            </div>
            <input class="stop-field" type="text" name="days[${dayIdx}][stops][${sIdx}][tip]" placeholder="Tips (opsional)">`;
    } else if (type === 'hotel') {
        const opts = hotelBookings.length
            ? hotelBookings.map(b => `<option value="${b.id}" data-price="${b.price}">${b.label}</option>`).join('')
            : '<option value="">Belum ada booking hotel</option>';
        bodyHTML = `
            <select class="stop-field" name="days[${dayIdx}][stops][${sIdx}][hotel_booking_id]" onchange="updateAutoTotal()">
                <option value="">Pilih booking hotel...</option>${opts}
            </select>
            <div class="stop-row">
                <input class="stop-field" type="text" name="days[${dayIdx}][stops][${sIdx}][custom_title]" placeholder="Atau ketik nama hotel manual">
                <input class="stop-cost-input" type="number" name="days[${dayIdx}][stops][${sIdx}][cost]" placeholder="Biaya (Rp)" onchange="updateAutoTotal()">
            </div>
            <input class="stop-field" type="text" name="days[${dayIdx}][stops][${sIdx}][tip]" placeholder="Tips (opsional)">`;
    } else if (type === 'place') {
        const opts = places.map(p => `<option value="${p.id}">${p.label}</option>`).join('');
        bodyHTML = `
            <select class="stop-field" name="days[${dayIdx}][stops][${sIdx}][place_id]">
                <option value="">Pilih dari database tempat...</option>${opts}
            </select>
            <div class="stop-row">
                <input class="stop-field" type="text" name="days[${dayIdx}][stops][${sIdx}][custom_place_name]" placeholder="Atau ketik nama tempat sendiri">
                <input class="stop-cost-input" type="number" name="days[${dayIdx}][stops][${sIdx}][cost]" placeholder="Biaya masuk (Rp)" onchange="updateAutoTotal()">
            </div>
            <div class="stop-row">
                <input class="stop-field" type="time" name="days[${dayIdx}][stops][${sIdx}][visit_time]" placeholder="Jam kunjungan">
                <input class="stop-field" type="number" name="days[${dayIdx}][stops][${sIdx}][duration_minutes]" placeholder="Lama kunjungan (menit)">
            </div>
            <input class="stop-field" type="text" name="days[${dayIdx}][stops][${sIdx}][tip]" placeholder="Tips untuk tempat ini">`;
    } else {
        bodyHTML = `
            <input class="stop-field" type="text" name="days[${dayIdx}][stops][${sIdx}][custom_title]" placeholder="Nama aktivitas *" required>
            <textarea class="stop-field" name="days[${dayIdx}][stops][${sIdx}][custom_description]" placeholder="Deskripsi aktivitas" rows="2"></textarea>
            <div class="stop-row">
                <input class="stop-cost-input" type="number" name="days[${dayIdx}][stops][${sIdx}][cost]" placeholder="Biaya (Rp)" onchange="updateAutoTotal()">
                <input class="stop-field" type="time" name="days[${dayIdx}][stops][${sIdx}][visit_time]" placeholder="Waktu">
            </div>`;
    }

    div.innerHTML = `
        <div class="stop-header">
            <span style="font-size:18px;">${typeIcons[type]}</span>
            <span style="font-size:13px;font-weight:600;color:var(--ink);">${typeLabels[type]}</span>
            <input type="hidden" name="days[${dayIdx}][stops][${sIdx}][type]" value="${type}">
            <input type="time" class="stop-time-input" name="days[${dayIdx}][stops][${sIdx}][visit_time_header]" placeholder="Jam">
            <button type="button" class="remove-stop-btn" onclick="document.getElementById('stop-${sIdx}').remove();updateAutoTotal();">✕</button>
        </div>
        <div class="stop-body">${bodyHTML}</div>`;

    list.appendChild(div);
}

// ── BUDGET TRACKER ────────────────────────────────────────────
function updateAutoTotal() {
    let total = 0;

    // Sum all linked flight bookings
    document.querySelectorAll('select[name*="flight_booking_id"]').forEach(sel => {
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.price) total += parseFloat(opt.dataset.price);
    });

    // Sum all linked hotel bookings
    document.querySelectorAll('select[name*="hotel_booking_id"]').forEach(sel => {
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.price) total += parseFloat(opt.dataset.price);
    });

    // Sum all manual cost fields
    document.querySelectorAll('input[name*="[cost]"]').forEach(inp => {
        if (inp.value) total += parseFloat(inp.value);
    });

    document.getElementById('autoTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function updateBudgetDisplay() {
    const val = document.getElementById('actualBudget').value;
    document.getElementById('manualBudgetDisplay').textContent = val
        ? 'Rp ' + parseFloat(val).toLocaleString('id-ID')
        : 'Belum diisi';
}

function setStatus(val) {
    document.getElementById('statusInput').value = val;
}
</script>
@endsection