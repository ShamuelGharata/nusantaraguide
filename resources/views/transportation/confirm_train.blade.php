@extends('layouts.app')
@section('title', 'Konfirmasi Kereta — NusantaraGuide')
@section('styles')
<style>
    .page-wrap{max-width:680px;margin:48px auto;padding:0 24px 80px;}
    .success-banner{background:linear-gradient(135deg,#1a1a35,#2d1a6a);border-radius:18px;padding:36px;text-align:center;margin-bottom:28px;color:white;}
    .success-icon{font-size:56px;margin-bottom:14px;}
    .success-banner h1{font-family:'Playfair Display',serif;font-size:26px;margin-bottom:8px;}
    .success-banner p{font-size:15px;opacity:0.8;}
    .booking-code-badge{display:inline-block;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);padding:8px 24px;border-radius:20px;margin-top:16px;font-size:18px;font-weight:700;letter-spacing:2px;}
    .detail-card{background:white;border-radius:18px;padding:28px;box-shadow:var(--card-shadow);margin-bottom:20px;}
    .detail-card h2{font-size:16px;font-weight:700;color:var(--forest);margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--cream);}
    .detail-row{display:flex;justify-content:space-between;align-items:flex-start;padding:12px 0;border-bottom:1px solid #f5f5f5;}
    .detail-row:last-child{border-bottom:none;}
    .detail-row .label{font-size:13px;color:var(--muted);}
    .detail-row .value{font-size:14px;font-weight:600;color:var(--ink);text-align:right;max-width:60%;}
    .status-badge{display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:20px;font-size:14px;font-weight:600;}
    .total-highlight{background:linear-gradient(135deg,#1a1a35,#2d1a6a);color:white;border-radius:12px;padding:20px 24px;display:flex;justify-content:space-between;align-items:center;margin-top:4px;}
    .total-highlight .tl{font-size:13px;opacity:0.75;}
    .total-highlight .tv{font-family:'Playfair Display',serif;font-size:24px;font-weight:900;}
    .actions{display:flex;gap:12px;flex-wrap:wrap;}
    .btn-primary{background:var(--jade);color:white;padding:12px 28px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;display:inline-block;transition:background 0.2s;}
    .btn-primary:hover{background:var(--forest);}
    .btn-outline{background:white;color:var(--coral);border:1.5px solid var(--coral);padding:12px 28px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;}
    .btn-outline:hover{background:var(--coral);color:white;}
    .btn-ghost{background:white;color:var(--muted);border:1.5px solid #e0dbd2;padding:12px 28px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;display:inline-block;transition:all 0.2s;}
    .btn-ghost:hover{border-color:var(--jade);color:var(--jade);}
</style>
@endsection
@section('content')
<div class="page-wrap">
    @if(session('success'))
    <div class="success-banner">
        <div class="success-icon">🎉</div>
        <h1>Tiket Berhasil Dipesan!</h1>
        <p>Tiket keretamu sudah masuk dan sedang menunggu konfirmasi.</p>
        <div class="booking-code-badge">{{ $booking->booking_code }}</div>
    </div>
    @endif
    <div class="detail-card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">Kode Booking</div>
            <div style="font-size:20px;font-weight:700;color:var(--ink);letter-spacing:1px;">{{ $booking->booking_code }}</div>
        </div>
        <span class="status-badge" style="background:{{ $booking->status_color }}22;color:{{ $booking->status_color }};border:1px solid {{ $booking->status_color }}44;">{{ $booking->status_label }}</span>
    </div>
    <div class="detail-card">
        <h2>Detail Kereta</h2>
        <div class="detail-row"><span class="label">Nama Kereta</span><span class="value">{{ $booking->train->name }}</span></div>
        <div class="detail-row"><span class="label">Kelas</span><span class="value">{{ $booking->train->class }}</span></div>
        <div class="detail-row"><span class="label">Rute</span><span class="value">{{ $booking->train->origin->name }} → {{ $booking->train->destination->name }}</span></div>
        <div class="detail-row"><span class="label">Tanggal</span><span class="value">{{ $booking->travel_date->isoFormat('dddd, D MMMM YYYY') }}</span></div>
        <div class="detail-row"><span class="label">Keberangkatan</span><span class="value">{{ substr($booking->train->departure_time,0,5) }}</span></div>
        <div class="detail-row"><span class="label">Kedatangan</span><span class="value">{{ substr($booking->train->arrival_time,0,5) }}</span></div>
        <div class="detail-row"><span class="label">Durasi</span><span class="value">{{ $booking->train->duration_label }}</span></div>
    </div>
    <div class="detail-card">
        <h2>Detail Pemesanan</h2>
        <div class="detail-row"><span class="label">Nama Penumpang</span><span class="value">{{ $booking->passenger_name }}</span></div>
        <div class="detail-row"><span class="label">No. Identitas</span><span class="value">{{ $booking->passenger_id_number }}</span></div>
        <div class="detail-row"><span class="label">No. Telepon</span><span class="value">{{ $booking->contact_phone }}</span></div>
        <div class="detail-row"><span class="label">Jumlah Penumpang</span><span class="value">{{ $booking->passengers }} orang</span></div>
        <div class="detail-row"><span class="label">Harga per orang</span><span class="value">{{ $booking->train->price_label }}</span></div>
        <div class="total-highlight"><span class="tl">Total Pembayaran</span><span class="tv">{{ $booking->total_label }}</span></div>
    </div>
    <div class="detail-card">
        <div class="actions">
            <a href="{{ route('bookings.my') }}" class="btn-primary">📋 Semua Booking Saya</a>
            <a href="{{ route('transportation.index', ['type'=>'train']) }}" class="btn-ghost">Cari Kereta Lain</a>
            @if($booking->status === 'pending')
            <form action="{{ route('transportation.train.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan tiket ini?')">
                @csrf<button type="submit" class="btn-outline">Batalkan Tiket</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection