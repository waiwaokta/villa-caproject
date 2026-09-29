@extends('layouts.app')

@section('title', $villa->name . ' - Villa')

@section('content')

<div class="wisma-detail">

    {{-- GALERI FOTO --}}
    <div class="gallery" data-total="{{ $villa->villaPhotos->count() }}">
        <div class="gallery-main">
            <img id="galleryMainImg"
                src="{{ route('dokumen.foto', $villa->primaryPhoto->photoID) }}"
                alt="{{ $villa->name }}">
        </div>
        @if($villa->villaPhotos->count() > 1)
            <div class="gallery-thumbs">
                @foreach($villa->villaPhotos as $photo)
                    <img
                        class="gallery-thumb {{ $photo->is_primary ? 'active' : '' }}"
                        src="{{ route('dokumen.foto', $photo->photoID) }}"
                        data-full="{{ route('dokumen.foto', $photo->photoID) }}"
                        alt="{{ $villa->name }}">
                @endforeach
            </div>
        @endif
    </div>

    <div class="wisma-detail-body">
        <div class="wisma-detail-main">

            {{-- INFO DASAR --}}
            <div class="wisma-header">
                @if($villa->location)
                    <div class="wisma-badge">
                        <i class="ti ti-map-pin" style="font-size:12px"></i>
                        {{ $villa->location }}
                    </div>
                @endif
                <h1>{{ $villa->name }}</h1>
                @if($villa->address)
                    <p class="wisma-address">
                        <i class="ti ti-map-2" style="font-size:13px"></i>
                        {{ $villa->address }}
                    </p>
                @endif
                <div class="wisma-meta-row">
                    @if($villa->capacity)
                        <span><i class="ti ti-users"></i> Kapasitas {{ $villa->capacity }} orang</span>
                    @endif
                    <span><i class="ti ti-clock-check"></i> Check-in 14:00</span>
                    <span><i class="ti ti-clock-pause"></i> Check-out 12:00</span>
                </div>
            </div>

            @if($villa->desc)
                <div class="wisma-section">
                    <h2>Tentang villa ini</h2>
                    <p class="wisma-desc">{{ $villa->desc }}</p>
                </div>
            @endif

            {{-- FASILITAS --}}
            @if($villa->facilities->count() > 0)
                <div class="wisma-section">
                    <h2>Fasilitas</h2>
                    <div class="facility-grid">
                        @foreach($villa->facilities as $facility)
                        <div class="facility-item">
                            <x-dynamic-component :component="$facility->icon"/>
                            <span>{{ $facility->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- KALENDER AVAILABILITY --}}
            <div class="wisma-section">
                <h2>Ketersediaan</h2>
                <p class="section-sub">Tanggal berwarna merah artinya sudah dibooking</p>

                <div class="cal-nav">
                    <button class="cal-nav-btn" id="calPrev">
                        <i class="ti ti-chevron-left"></i>
                    </button>
                    <div class="cal-nav-selectors">
                        <select id="calMonthSelect" class="cal-select">
                            <option value="0">Januari</option>
                            <option value="1">Februari</option>
                            <option value="2">Maret</option>
                            <option value="3">April</option>
                            <option value="4">Mei</option>
                            <option value="5">Juni</option>
                            <option value="6">Juli</option>
                            <option value="7">Agustus</option>
                            <option value="8">September</option>
                            <option value="9">Oktober</option>
                            <option value="10">November</option>
                            <option value="11">Desember</option>
                        </select>
                        <select id="calYearSelect" class="cal-select"></select>
                    </div>
                    <button class="cal-nav-btn" id="calNext">
                        <i class="ti ti-chevron-right"></i>
                    </button>
                </div>

                <div class="calendar-wrap-single" id="calendarWrap"
                    data-occupied="{{ json_encode(array_keys($availability)) }}">
                </div>

                <div class="calendar-legend">
                    <span><i class="legend-dot legend-available"></i> Tersedia</span>
                    <span><i class="legend-dot legend-occupied"></i> Sudah dibooking</span>
                    <span><i class="legend-dot legend-past"></i> Tanggal lewat</span>
                </div>
            </div>
        </div>

        {{-- SIDEBAR HARGA + CTA --}}
        <div class="wisma-sidebar">
            <div class="price-card">
                <h3>Daftar Harga</h3>
                <div class="price-table">
                    <div class="price-row price-row-header">
                        <span></span>
                        <span>Harga</span>
                    </div>
                    @foreach(['weekday' => 'Senin–Jumat', 'weekend' => 'Sabtu–Minggu', 'holiday' => 'Libur Panjang'] as $type => $label)
                        @php
                            $harga = $villa->prices->where('day_type', $type)->first();
                        @endphp
                        <div class="price-row">
                            <span class="price-label">{{ $label }}</span>
                            <span class="price-value">{{ $harga ? 'Rp ' . number_format($harga->price, 0, ',', '.') : '-' }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="price-note">* Harga per malam</p>

                <a href="/booking/{{ $villa->villaID }}?check_in={{ $prefillCheckIn }}&check_out={{ $prefillCheckOut }}" class="btn-book-now">
                    <i class="ti ti-calendar-plus"></i> Booking Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="lightbox-overlay" id="lightboxOverlay">
    <button class="lightbox-close" id="lightboxClose">
        <i class="ti ti-x"></i>
    </button>
    <div class="lightbox-img-wrap">
        <button class="lightbox-btn-prev" id="lightboxPrev">
            <i class="ti ti-chevron-left"></i>
        </button>
        <img src="" alt="Foto Villa" id="lightboxImg">
        <button class="lightbox-btn-next" id="lightboxNext">
            <i class="ti ti-chevron-right"></i>
        </button>
    </div>
    <div class="lightbox-thumbs" id="lightboxThumbs"></div>
    <div class="lightbox-counter" id="lightboxCounter"></div>
</div>

@endsection