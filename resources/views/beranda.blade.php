@extends('layouts.app')

@section('title', 'Wisma PLN - Penginapan Resmi PLN')

@push('styles')
@endpush

@section('content')

{{-- TICKER STATISTIK BOOKING — sticky di bawah nav --}}
<div class="hero-section-wrap">
    <div class="stat-ticker-wrap" id="statTicker">
        <div class="stat-ticker-track" id="statTickerTrack">
            @foreach($bookingStats as $stat)
                <div class="stat-ticker-item">
                    <span class="stat-ticker-name">{{ $stat->name }}</span>
                    <span class="stat-ticker-count">{{ $stat->bookings_count }} booking bulan ini</span>
                </div>
            @endforeach
        </div>
    </div>

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
</div>

@endsection

@push('scripts')
@endpush