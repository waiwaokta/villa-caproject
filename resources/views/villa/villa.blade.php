@extends('layouts.app')

@section('title', 'Cari Villa')

@push('styles')
@endpush

@section('content')

<div class="villa-page">
    <div class="villa-page-header">
        <h1>Cari Villa</h1>
        <p>Pilih lokasi dan tanggal untuk melihat villa yang tersedia</p>
    </div>

    <form class="search-box villa-box-standalone" method="GET" action="{{ route('villa.search') }}">
        <div class="sf">
            <label>Lokasi</label>
            <select name="lokasi">
                <option value="">Semua lokasi</option>
                @foreach($lokasi as $l)
                    <option value="{{ $l }}" {{ $lokasiTerpilih == $l ? 'selected' : '' }}>
                        {{ $l }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="sf">
            <label>Check-in</label>
            <input type="date" name="check_in" value="{{ $checkIn }}" min="{{ date('Y-m-d') }}">
        </div>
        <div class="sf">
            <label>Check-out</label>
            <input type="date" name="check_out" value="{{ $checkOut }}">
        </div>
        <button type="submit" class="search-btn">
            <i class="ti ti-search"></i> Cari
        </button>
    </form>

    <div class="villa-result-info">
        <p>{{ count($villas) }} villa tersedia untuk tanggal {{ \Carbon\Carbon::parse($checkIn)->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($checkOut)->translatedFormat('d M Y') }}</p>
    </div>

    @if($villas->isEmpty())
        <div class="villa-empty">
            <i class="ti ti-calendar-off"></i>
            <p>Villa tidak tersedia untuk tanggal dan lokasi yang dipilih.</p>
            <p class="villa-empty-sub">Coba ganti tanggal atau lokasi pencarian.</p>
        </div>
        @if ($rekomendasi->isNotEmpty())
            <div class="villa-rekomendasi">
                <p class="villa-rekomendasi-label">Villa Lain yang Tersedia</p>

                <div class="ticker-card-wrap">
                    <div class="ticker-card-track" id="rekomendasiTrack">
                        @foreach ($rekomendasi as $villa)
                            @php
                                $harga = $villa->prices->where('day_type', 'weekday')->first();
                            @endphp
                            <a href="{{ route('villa.show', $villa->villaID) }}" class="ticker-card">
                                <div class="ticker-card-img">
                                    @if ($villa->primaryPhoto)
                                        <img src="{{ route('dokumen.foto', $villa->primaryPhoto->photoID) }}" alt="{{ $villa->name }}" loading="lazy">
                                    @else
                                        <div class="ticker-card-img-fill"><i class="ti ti-home-2"></i></div>
                                    @endif
                                </div>
                                <div class="ticker-card-body">
                                    <p class="ticker-card-name">{{ $villa->name }}</p>
                                    <p class="ticker-card-location"><i class="ti ti-map-pin"></i> {{ $villa->location }}</p>
                                    @if ($harga)
                                        <p class="ticker-card-price">Mulai Rp {{ number_format($harga->price, 0, ',', '.') }} <span>/ malam</span></p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="villa-result-list">
            @foreach($villas as $villa)
                @php
                    $foto = $villa->primaryPhoto;
                    $harga = $villa->prices->where('day_type', 'weekday')->first();
                    $urlDetail = route('villa.show', $villa->villaID) . '?check_in=' . $checkIn . '&check_out=' . $checkOut;
                @endphp
                <div class="villa-card" data-href="{{ $urlDetail }}">
                    <div class="villa-card-img">
                        @if($foto)
                            <img src="{{ route('dokumen.foto', $foto->photoID) }}" alt="{{ $villa->name }}" loading="lazy">
                        @else
                            <div class="villa-card-img-fill">
                                <i class="ti ti-home-2"></i>
                            </div>
                        @endif
                    </div>
                    <div class="villa-card-body">
                        <h3 class="villa-card-title">{{ $villa->name }}</h3>
                        @if($villa->location)
                            <p class="villa-card-location">
                                <i class="ti ti-map-pin"></i> {{ $villa->location }}
                            </p>
                        @endif
                        @if($villa->desc)
                            <p class="villa-card-desc">{{ Str::limit($villa->desc, 120) }}</p>
                        @endif
                        <div class="villa-card-footer">
                            @if($harga)
                                <div class="villa-card-price">
                                    <span>Mulai dari</span>
                                    <strong>Rp {{ number_format($harga->price, 0, ',', '.') }}</strong>
                                    <span>/ malam</span>
                                </div>
                            @endif
                           <a href="{{ route('booking.create', $villa->villaID) }}?check_in={{ $checkIn }}&check_out={{ $checkOut }}" class="villa-card-btn" data-stop-card-click="1">Pesan sekarang</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
@endpush