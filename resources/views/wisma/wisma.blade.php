@extends('layouts.app')

@section('title', 'Cari Wisma - Wisma PLN')

@push('styles')
@endpush

@section('content')

<div class="wisma-page">
    <div class="wisma-page-header">
        <h1>Cari Wisma</h1>
        <p>Pilih lokasi dan tanggal untuk melihat wisma yang tersedia</p>
    </div>

    <form class="search-box wisma-box-standalone" method="GET" action="{{ route('wisma.search') }}">
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

    <div class="wisma-result-info">
        <p>{{ count($wismas) }} wisma tersedia untuk tanggal {{ \Carbon\Carbon::parse($checkIn)->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($checkOut)->translatedFormat('d M Y') }}</p>
    </div>

    @if($wismas->isEmpty())
        <div class="wisma-empty">
            <i class="ti ti-calendar-off"></i>
            <p>Wisma tidak tersedia untuk tanggal dan lokasi yang dipilih.</p>
            <p class="wisma-empty-sub">Coba ganti tanggal atau lokasi pencarian.</p>
        </div>
    @else
        <div class="wisma-result-list">
            @foreach($wismas as $wisma)
                @php
                    $foto = $wisma->primaryPhoto;
                    $hargaPln = $wisma->prices
                        ->where('user_type', 'pln')
                        ->where('day_type', 'weekday')
                        ->first();
                    $urlDetail = route('wisma.show', $wisma->wismaID) . '?check_in=' . $checkIn . '&check_out=' . $checkOut;
                @endphp
                <div class="wisma-card" data-href="{{ $urlDetail }}">
                    <div class="wisma-card-img">
                        @if($foto)
                            <img src="{{ route('dokumen.foto', $foto->photoID) }}" alt="{{ $wisma->name }}" loading="lazy">
                        @else
                            <div class="wisma-card-img-fill">
                                <i class="ti ti-home-2"></i>
                            </div>
                        @endif
                    </div>
                    <div class="wisma-card-body">
                        <h3 class="wisma-card-title">{{ $wisma->name }}</h3>
                        @if($wisma->location)
                            <p class="wisma-card-location">
                                <i class="ti ti-map-pin"></i> {{ $wisma->location }}
                            </p>
                        @endif
                        @if($wisma->desc)
                            <p class="wisma-card-desc">{{ Str::limit($wisma->desc, 120) }}</p>
                        @endif
                        <div class="wisma-card-footer">
                            @if($hargaPln)
                                <div class="wisma-card-price">
                                    <span>Mulai dari (PLN)</span>
                                    <strong>Rp {{ number_format($hargaPln->price, 0, ',', '.') }}</strong>
                                    <span>/ malam</span>
                                </div>
                            @endif
                           <a href="{{ route('booking.create', $wisma->wismaID) }}?check_in={{ $checkIn }}&check_out={{ $checkOut }}" class="wisma-card-btn" data-stop-card-click="1">Pesan sekarang</a>
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