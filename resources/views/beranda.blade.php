@extends('layouts.app')

@section('title', 'Wisma PLN - Penginapan Resmi PLN')

@push('styles')
<style>
    /* HERO SLIDER BLOCK */
    .hero-slider-block {
        background: linear-gradient(160deg,
            #0a3a6e 0%,
            #0C447C 30%,
            #0e5490 50%,
            #042C53 75%,
            #021a36 100%
        );
        position: relative;
    }

    /* HERO */
    .hero { padding: 64px 40px 40px; text-align: center; position: relative; }
    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 50% 0%, rgba(0,163,173,0.15) 0%, transparent 65%);
        pointer-events: none;
    }
    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(0,163,173,0.15);
        border: 1px solid rgba(0,163,173,0.3);
        color: #4DD9E0;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 100px;
        margin-bottom: 20px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .hero h1 {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
        letter-spacing: -0.03em;
        line-height: 1.2;
    }
    .hero h1 span { color: #4DD9E0; }
    .hero p {
        color: rgba(181,212,244,0.8);
        font-size: 14px;
        margin-bottom: 32px;
        line-height: 1.6;
    }

    /* SEARCH BOX */
    .search-box {
        background: rgba(255,255,255,0.97);
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        gap: 12px;
        align-items: flex-end;
        max-width: 760px;
        margin: 0 auto;
        box-shadow: 0 20px 60px rgba(2,26,54,0.35), 0 0 0 1px rgba(255,255,255,0.1);
    }
    .sf { flex: 1; display: flex; flex-direction: column; gap: 5px; }
    .sf label {
        font-size: 10px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: .08em;
    }
    .sf select, .sf input {
        border: 1.5px solid #ebebeb;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 13px;
        font-weight: 500;
        color: #1a1a18;
        background: #fff;
        width: 100%;
        font-family: inherit;
        transition: border-color .2s;
        outline: none;
    }
    .sf select:focus, .sf input:focus { border-color: var(--blue-accent); }
    .search-btn {
        background: linear-gradient(135deg, var(--blue) 0%, var(--blue-accent) 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        font-family: inherit;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: opacity .2s;
        box-shadow: 0 4px 14px rgba(0,163,173,0.35);
    }
    .search-btn:hover { opacity: 0.9; }

    /* SLIDER HEADER */
    .fw-slider-header {
        padding: 40px 40px 18px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .fw-slider-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 4px;
        letter-spacing: -0.02em;
    }
    .fw-slider-header p { font-size: 13px; color: rgba(181,212,244,0.7); }
    .fw-slider-hint {
        font-size: 12px;
        color: rgba(107,163,212,0.8);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* SLIDER */
    .fw-wrap { position: relative; overflow: hidden; cursor: grab; }
    .fw-wrap:active { cursor: grabbing; }
    .fw-track { display: flex; transition: transform .45s cubic-bezier(.4,0,.2,1); }
    .fw-slide { min-width: 100%; position: relative; }
    .fw-slide-bg {
        height: 70vh;
        min-height: 420px;
        display: flex;
        align-items: flex-end;
        position: relative;
        overflow: hidden;
    }
    .fw-bg-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 8s ease;
    }
    .fw-wrap:hover .fw-bg-img { transform: scale(1.03); }
    .fw-bg-fill {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #042C53, #0e5490);
    }
    .fw-bg-fill i { font-size: 140px; opacity: .08; color: #fff; }
    .fw-top-fade {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 120px;
        background: linear-gradient(to bottom, rgba(4,28,55,0.7) 0%, transparent 100%);
        z-index: 1;
    }
    .fw-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to top,
            rgba(2,26,54,0.97) 0%,
            rgba(2,26,54,0.5) 45%,
            rgba(2,26,54,0.1) 75%,
            transparent 100%
        );
    }
    .fw-content {
        position: relative;
        z-index: 3;
        padding: 32px 48px;
        width: 100%;
    }
    .fw-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(0,163,173,0.2);
        border: 1px solid rgba(0,163,173,0.4);
        color: #4DD9E0;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 100px;
        margin-bottom: 14px;
        letter-spacing: 0.04em;
    }
    .fw-title {
        color: #fff;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .fw-desc {
        color: rgba(181,212,244,0.75);
        font-size: 13px;
        line-height: 1.75;
        margin-bottom: 18px;
        max-width: 520px;
    }
    .fw-meta { display: flex; gap: 20px; margin-bottom: 24px; }
    .fw-meta span {
        font-size: 12px;
        color: rgba(133,183,235,0.8);
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 500;
    }
    .fw-actions { display: flex; gap: 12px; align-items: center; }
    .fw-btn-book {
        background: linear-gradient(135deg, #fff 0%, #f0f7ff 100%);
        color: var(--blue-dark);
        border: none;
        border-radius: 10px;
        padding: 11px 24px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    }
    .fw-btn-book:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.25); }
    .fw-btn-detail {
        background: rgba(255,255,255,0.1);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 10px;
        padding: 11px 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        backdrop-filter: blur(8px);
        transition: background .2s;
    }
    .fw-btn-detail:hover { background: rgba(255,255,255,0.18); }
    .fw-price {
        margin-left: 8px;
        padding-left: 20px;
        border-left: 1px solid rgba(255,255,255,0.15);
    }
    .fw-price .fw-price-label { font-size: 11px; color: rgba(133,183,235,0.7); display: block; font-weight: 500; }
    .fw-price strong { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
    .fw-price .fw-price-sub { font-size: 11px; color: rgba(133,183,235,0.7); display: block; }

    /* COUNTER & DOTS */
    .fw-counter {
        position: absolute;
        top: 16px;
        right: 48px;
        color: rgba(255,255,255,0.4);
        font-size: 12px;
        font-weight: 600;
        z-index: 10;
        letter-spacing: 0.05em;
    }
    .fw-dots {
        position: absolute;
        bottom: 20px;
        right: 48px;
        display: flex;
        gap: 6px;
        z-index: 10;
    }
    .fw-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
        cursor: pointer;
        transition: all .25s;
        border: none;
    }
    .fw-dot.active { width: 22px; border-radius: 3px; background: #fff; }

    /* FADE KE CREAM */
    .slider-bottom-fade {
        height: 64px;
        background: linear-gradient(to bottom, #021a36 0%, var(--cream) 100%);
        margin-top: -1px;
    }

    /* WHY GRID */
    .why-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
    .why-card {
        background: #fff;
        border-radius: 14px;
        padding: 24px;
        border: 1px solid var(--border);
        transition: transform .2s, box-shadow .2s;
    }
    .why-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(24,95,165,0.08);
    }
    .why-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #E6F1FB, #d0e8f8);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
    .why-icon i { font-size: 22px; color: var(--blue); }
    .why-title { font-size: 14px; font-weight: 700; margin-bottom: 7px; letter-spacing: -0.01em; }
    .why-desc { font-size: 12px; color: var(--text-secondary); line-height: 1.75; }

    /* STEPS */
    .steps-wrap {
        display: grid;
        grid-template-columns: repeat(4,1fr);
        gap: 0;
        position: relative;
    }
    .steps-wrap::before {
        content: '';
        position: absolute;
        top: 22px;
        left: 12.5%;
        right: 12.5%;
        height: 1px;
        background: linear-gradient(to right, transparent, var(--border), var(--border), transparent);
        z-index: 0;
    }
    .step { text-align: center; padding: 0 16px; position: relative; z-index: 1; }
    .step-num {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--blue) 0%, var(--blue-accent) 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        margin: 0 auto 14px;
        box-shadow: 0 4px 14px rgba(24,95,165,0.3);
    }
    .step-title { font-size: 13px; font-weight: 700; margin-bottom: 6px; }
    .step-desc { font-size: 12px; color: var(--text-secondary); line-height: 1.65; }

    /* TESTIMONI */
    .testi-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
    .testi-card {
        background: #fff;
        border-radius: 14px;
        padding: 22px;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }
    .testi-card::before {
        content: '"';
        position: absolute;
        top: -10px;
        right: 16px;
        font-size: 80px;
        color: var(--blue-accent);
        opacity: 0.08;
        font-family: Georgia, serif;
        line-height: 1;
    }
    .testi-stars { display: flex; gap: 3px; margin-bottom: 12px; }
    .testi-stars i { font-size: 13px; color: #F0A500; }
    .testi-text {
        font-size: 13px;
        line-height: 1.75;
        margin-bottom: 16px;
        color: #3d3d3a;
        font-weight: 400;
    }
    .testi-user { display: flex; align-items: center; gap: 10px; }
    .testi-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--blue-lighter), #d0e8f8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: var(--blue-dark);
        flex-shrink: 0;
    }
    .testi-name { font-size: 13px; font-weight: 700; }
    .testi-sub { font-size: 11px; color: var(--text-secondary); margin-top: 1px; }
</style>
@endpush

@section('content')

{{-- HERO + SLIDER --}}
<div class="hero-slider-block">
    <div class="hero">
        <div class="hero-eyebrow">
            <i class="ti ti-building-community" style="font-size:12px"></i>
            Penginapan Resmi PT PLN (Persero)
        </div>
        <h1>Temukan <span>Wisma PLN</span><br>untuk liburan Anda</h1>
        <p>Tersedia di berbagai destinasi — harga spesial untuk pegawai & pensiunan PLN</p>
        <div class="search-box">
            <div class="sf">
                <label>Lokasi</label>
                <select name="lokasi" id="filter-lokasi">
                    <option value="">Semua lokasi</option>
                    @foreach($lokasi as $l)
                        <option value="{{ $l }}" {{ request('lokasi') == $l ? 'selected' : '' }}>
                            {{ $l }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sf">
                <label>Check-in</label>
                <input type="date" id="filter-checkin" min="{{ date('Y-m-d') }}" value="{{ request('check_in') }}">
            </div>
            <div class="sf">
                <label>Check-out</label>
                <input type="date" id="filter-checkout" value="{{ request('check_out') }}">
            </div>
            <div class="sf" style="flex:.7">
                <label>Status</label>
                <select name="user_type" id="filter-usertype">
                    <option value="umum" {{ request('user_type','umum') == 'umum' ? 'selected' : '' }}>Umum</option>
                    <option value="pln" {{ request('user_type') == 'pln' ? 'selected' : '' }}>Pegawai PLN</option>
                </select>
            </div>
            <button class="search-btn" onclick="doSearch()">
                <i class="ti ti-search"></i> Cari
            </button>
        </div>
    </div>

    <div class="fw-slider-header" id="wisma">
        <div>
            <h2>Wisma tersedia</h2>
            <p>Geser untuk melihat semua wisma yang tersedia</p>
        </div>
    </div>

    <div class="fw-wrap" id="fwWrap" data-total="{{ count($wismas) }}">
        <div class="fw-track" id="fwTrack">
            @foreach($wismas as $wisma)
                @php
                    $foto = $wisma->primaryPhoto;
                    $hargaPln = $wisma->prices
                        ->where('user_type', 'pln')
                        ->where('day_type', 'weekday')
                        ->first();
                @endphp
                <div class="fw-slide">
                    <div class="fw-slide-bg">
                        @if($foto)
                            <img class="fw-bg-img"
                                src="{{ route('dokumen.foto', $foto->photoID) }}"
                                alt="{{ $wisma->name }}"
                                loading="lazy">
                        @else
                            <div class="fw-bg-fill">
                                <i class="ti ti-home-2"></i>
                            </div>
                        @endif
                        <div class="fw-top-fade"></div>
                        <div class="fw-overlay"></div>
                        <div class="fw-content">
                            @if($wisma->location)
                                <div class="fw-badge">
                                    <i class="ti ti-map-pin" style="font-size:11px"></i>
                                    {{ $wisma->location }}
                                </div>
                            @endif
                            <h3 class="fw-title">{{ $wisma->name }}</h3>
                            @if($wisma->desc)
                                <p class="fw-desc">{{ Str::limit($wisma->desc, 110) }}</p>
                            @endif
                            <div class="fw-meta">
                                @if($wisma->capacity)
                                    <span>
                                        <i class="ti ti-users" style="font-size:13px"></i>
                                        Kapasitas {{ $wisma->capacity }} orang
                                    </span>
                                @endif
                                @if($wisma->location)
                                    <span>
                                        <i class="ti ti-map-pin" style="font-size:13px"></i>
                                        {{ $wisma->location }}
                                    </span>
                                @endif
                            </div>
                            <div class="fw-actions">
                                <a href="/booking/{{ $wisma->wismaID }}" class="fw-btn-book">
                                    <i class="ti ti-calendar-plus"></i> Pesan sekarang
                                </a>
                                <a href="/wisma/{{ $wisma->wismaID }}" class="fw-btn-detail">
                                    Lihat detail
                                </a>
                                @if($hargaPln)
                                    <div class="fw-price">
                                        <span class="fw-price-label">Mulai dari (PLN)</span>
                                        <strong>Rp {{ number_format($hargaPln->price, 0, ',', '.') }}</strong>
                                        <span class="fw-price-sub">/ malam</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="fw-counter" id="fwCounter">1 / {{ count($wismas) }}</div>
        <div class="fw-dots" id="fwDots"></div>
    </div>
</div>

<div class="slider-bottom-fade"></div>

{{-- KENAPA PILIH WISMA PLN --}}
<div class="section" id="tentang">
    <div class="section-header">
        <h2>Kenapa pilih wisma PLN?</h2>
        <p>Fasilitas terpercaya dengan harga yang transparan</p>
    </div>
    <div class="why-grid">
        <div class="why-card">
            <div class="why-icon"><i class="ti ti-shield-check"></i></div>
            <p class="why-title">Terpercaya & resmi</p>
            <p class="why-desc">Dikelola langsung oleh PT PLN (Persero). Fasilitas terawat dan terjamin kualitasnya.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="ti ti-tag"></i></div>
            <p class="why-title">Harga spesial PLN</p>
            <p class="why-desc">Tarif khusus untuk pegawai dan pensiunan PLN. Lebih hemat dibanding tarif umum.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="ti ti-map-2"></i></div>
            <p class="why-title">Lokasi strategis</p>
            <p class="why-desc">Tersebar di destinasi wisata populer dengan akses mudah dan lingkungan nyaman.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="ti ti-calendar-check"></i></div>
            <p class="why-title">Booking mudah</p>
            <p class="why-desc">Proses pemesanan online, cukup isi form dan unggah dokumen. Konfirmasi cepat.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="ti ti-home-2"></i></div>
            <p class="why-title">Fasilitas lengkap</p>
            <p class="why-desc">Ruang keluarga, dapur, parkir luas, dan lingkungan bersih di setiap wisma.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="ti ti-headset"></i></div>
            <p class="why-title">Dukungan admin</p>
            <p class="why-desc">Tim admin siap membantu proses booking dan konfirmasi ketersediaan wisma.</p>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- CARA BOOKING --}}
<div class="section">
    <div class="section-header">
        <h2>Cara booking</h2>
        <p>Proses mudah, selesai dalam beberapa menit</p>
    </div>
    <div class="steps-wrap">
        <div class="step">
            <div class="step-num">1</div>
            <p class="step-title">Cari wisma</p>
            <p class="step-desc">Filter berdasarkan lokasi dan tanggal yang diinginkan</p>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <p class="step-title">Isi form booking</p>
            <p class="step-desc">Lengkapi data diri, tanggal menginap, dan unggah dokumen</p>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <p class="step-title">Lakukan pembayaran</p>
            <p class="step-desc">Transfer ke rekening yang tertera dan unggah bukti bayar</p>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <p class="step-title">Tunggu konfirmasi</p>
            <p class="step-desc">Admin verifikasi dan kirim konfirmasi via WhatsApp</p>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- TESTIMONI --}}
<div class="section" id="kontak">
    <div class="section-header">
        <h2>Kata mereka</h2>
        <p>Pengalaman tamu yang telah menginap di wisma PLN</p>
    </div>
    <div class="testi-grid">
        <div class="testi-card">
            <div class="testi-stars">
                @for($i = 0; $i < 5; $i++)<i class="ti ti-star-filled"></i>@endfor
            </div>
            <p class="testi-text">"Wisma PLN tempatnya bersih, udaranya sejuk. Cocok banget buat liburan keluarga. Pasti balik lagi!"</p>
            <div class="testi-user">
                <div class="testi-avatar">AR</div>
                <div>
                    <p class="testi-name">Agus Riyanto</p>
                    <p class="testi-sub">Pegawai PLN</p>
                </div>
            </div>
        </div>
        <div class="testi-card">
            <div class="testi-stars">
                @for($i = 0; $i < 5; $i++)<i class="ti ti-star-filled"></i>@endfor
            </div>
            <p class="testi-text">"Proses bookingnya gampang, adminnya responsif. Viewnya luar biasa, recommended!"</p>
            <div class="testi-user">
                <div class="testi-avatar">SW</div>
                <div>
                    <p class="testi-name">Siti Wahyuni</p>
                    <p class="testi-sub">Umum</p>
                </div>
            </div>
        </div>
        <div class="testi-card">
            <div class="testi-stars">
                @for($i = 0; $i < 5; $i++)<i class="ti ti-star-filled"></i>@endfor
            </div>
            <p class="testi-text">"Harga PLN jauh lebih terjangkau. Fasilitas lengkap, tempatnya nyaman. Recommended buat keluarga!"</p>
            <div class="testi-user">
                <div class="testi-avatar">DP</div>
                <div>
                    <p class="testi-name">Dwi Prasetyo</p>
                    <p class="testi-sub">Pensiunan PLN</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const fwWrap   = document.getElementById('fwWrap');
    const fwTrack  = document.getElementById('fwTrack');
    const fwDotsEl = document.getElementById('fwDots');
    const total    = parseInt(fwWrap.dataset.total, 10);
    let cur = 0, startX = 0, isDragging = false, accumX = 0, scrollLocked = false;

    for (let i = 0; i < total; i++) {
        const d = document.createElement('div');
        d.className = 'fw-dot' + (i === 0 ? ' active' : '');
        d.onclick = () => goTo(i);
        fwDotsEl.appendChild(d);
    }

    function goTo(n) {
        cur = Math.max(0, Math.min(n, total - 1));
        fwTrack.style.transform = `translateX(-${cur * 100}%)`;
        document.getElementById('fwCounter').textContent = `${cur + 1} / ${total}`;
        document.querySelectorAll('.fw-dot').forEach((d, i) => d.classList.toggle('active', i === cur));
    }

    fwWrap.addEventListener('wheel', e => {
        const isH = Math.abs(e.deltaX) > Math.abs(e.deltaY);
        if (!isH) return;
        e.preventDefault();
        if (scrollLocked) return;
        accumX += e.deltaX;
        if (Math.abs(accumX) > 50) {
            goTo(accumX > 0 ? cur + 1 : cur - 1);
            accumX = 0;
            scrollLocked = true;
            setTimeout(() => { scrollLocked = false; }, 500);
        }
    }, { passive: false });

    fwWrap.addEventListener('touchstart', e => { startX = e.touches[0].clientX; isDragging = true; }, { passive: true });
    fwWrap.addEventListener('touchend', e => {
        if (!isDragging) return;
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(diff > 0 ? cur + 1 : cur - 1);
        isDragging = false;
    });

    fwWrap.addEventListener('mousedown', e => { startX = e.clientX; isDragging = true; e.preventDefault(); });
    document.addEventListener('mouseup', e => {
        if (!isDragging) return;
        const diff = startX - e.clientX;
        if (Math.abs(diff) > 40) goTo(diff > 0 ? cur + 1 : cur - 1);
        isDragging = false;
    });

    setInterval(() => goTo(cur + 1 < total ? cur + 1 : 0), 5500);

    function doSearch() {
        const lokasi   = document.getElementById('filter-lokasi').value;
        const checkin  = document.getElementById('filter-checkin').value;
        const checkout = document.getElementById('filter-checkout').value;
        const usertype = document.getElementById('filter-usertype').value;
        const params   = new URLSearchParams({
            lokasi,
            check_in: checkin,
            check_out: checkout,
            user_type: usertype
        });
        window.location.href = '/?' + params.toString();
    }

    document.getElementById('filter-checkin').addEventListener('change', function() {
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        document.getElementById('filter-checkout').min = nextDay.toISOString().split('T')[0];
    });
</script>
@endpush