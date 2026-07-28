@extends('layouts.app')

@section('title', 'Cek Booking - Wisma PLN')

@section('content')

<div class="cekbooking-page">
    <div class="cekbooking-header">
        <h1>Cek Status Booking</h1>
        <p>Masukkan kode booking Anda untuk melihat status terkini pemesanan.</p>
    </div>

    {{-- BOX PENCARIAN --}}
    <div class="cekbooking-search-box">
        <form action="{{ route('booking.cek.cari') }}" method="POST" class="cekbooking-search-form">
            @csrf
            <div class="form-group" style="margin-bottom:0; flex:1;">
                <label>Kode Booking</label>
                <input type="text" name="booking_code" required placeholder="Contoh: 9c3d1a2b-xxxx-xxxx-xxxx-xxxxxxxxxxxx" value="{{ old('booking_code') }}">
            </div>
            <button type="submit" class="btn-cek-booking">
                <i class="ti ti-search"></i> Cek
            </button>
        </form>
        @if ($errors->any())
            <div class="form-error" style="display:block; margin-top:14px;">
                {{ $errors->first('booking_code') }}
            </div>
        @endif
    </div>

    @isset($booking)
        @php
            $statusConfig = [
                'pending'  => ['label' => 'Menunggu Persetujuan', 'icon' => 'ti-clock', 'color' => 'pending'],
                'approved' => ['label' => 'Disetujui', 'icon' => 'ti-circle-check', 'color' => 'approved'],
                'rejected' => ['label' => 'Tidak Disetujui', 'icon' => 'ti-circle-x', 'color' => 'rejected'],
            ];
            $current = $statusConfig[$booking->status];
        @endphp

        {{-- BOX RINGKASAN BOOKING --}}
        <div class="cekbooking-result-box">
            <div class="cekbooking-result-header">
                <div>
                    <p class="cekbooking-result-label">Kode Booking</p>
                    <p class="cekbooking-result-code">{{ $booking->bookingID }}</p>
                </div>
                <div class="cekbooking-status-badge cekbooking-status-{{ $current['color'] }}">
                    <i class="ti {{ $current['icon'] }}"></i> {{ $current['label'] }}
                </div>
            </div>

            <div class="cekbooking-result-info">
                <div class="cekbooking-info-item">
                    <span>Nama</span>
                    <strong>{{ $booking->guest_name }}</strong>
                </div>
                <div class="cekbooking-info-item">
                    <span>Wisma</span>
                    <strong>{{ $booking->wisma->name ?? '-' }}</strong>
                </div>
                <div class="cekbooking-info-item">
                    <span>Lokasi</span>
                    <strong>{{ $booking->wisma->location ?? '-' }}</strong>
                </div>
                <div class="cekbooking-info-item">
                    <span>Check-in</span>
                    <strong>{{ \Carbon\Carbon::parse($booking->check_in)->translatedFormat('d F Y') }}</strong>
                </div>
                <div class="cekbooking-info-item">
                    <span>Check-out</span>
                    <strong>{{ \Carbon\Carbon::parse($booking->check_out)->translatedFormat('d F Y') }}</strong>
                </div>
                <div class="cekbooking-info-item">
                    <span>Total Biaya</span>
                    <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        {{-- TIMELINE TRACKING --}}
        <div class="cekbooking-timeline-box">
            <h3>Riwayat Status</h3>

            <div class="timeline">
                {{-- TITIK 1 — selalu ada, booking terkirim --}}
                <div class="timeline-item timeline-done">
                    <div class="timeline-dot">
                        <i class="ti ti-send"></i>
                    </div>
                    <div class="timeline-content">
                        <p class="timeline-title">Booking Berhasil Dikirim</p>
                        <p class="timeline-desc">Form booking Anda telah kami terima dan tercatat dalam sistem.</p>
                        <p class="timeline-date">{{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }} WIB</p>
                    </div>
                </div>

                {{-- TITIK 2 — selalu ada, sedang dipantau --}}
                <div class="timeline-item timeline-done">
                    <div class="timeline-dot">
                        <i class="ti ti-eye"></i>
                    </div>
                    <div class="timeline-content">
                        <p class="timeline-title">Sedang Dipantau Tim Kami</p>
                        <p class="timeline-desc">Tim kami sedang memverifikasi data dan dokumen booking Anda.</p>
                    </div>
                </div>

                {{-- TITIK 3 — kondisional sesuai status akhir --}}
                @if ($booking->status === 'pending')
                    <div class="timeline-item timeline-current">
                        <div class="timeline-dot timeline-dot-pending">
                            <i class="ti ti-clock"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Menunggu Keputusan</p>
                            <p class="timeline-desc">Booking Anda masih dalam proses peninjauan. Mohon tunggu konfirmasi selanjutnya melalui WhatsApp.</p>
                        </div>
                    </div>
                @elseif ($booking->status === 'approved')
                    <div class="timeline-item timeline-done timeline-success">
                        <div class="timeline-dot timeline-dot-approved">
                            <i class="ti ti-circle-check"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Booking Telah Disetujui</p>
                            <p class="timeline-desc">Selamat! Booking Anda telah disetujui oleh tim kami. Terima kasih telah memilih Wisma PLN, kami tunggu kedatangan Anda.</p>
                            <p class="timeline-date">{{ \Carbon\Carbon::parse($booking->updated_at)->translatedFormat('d F Y, H:i') }} WIB</p>
                        </div>
                    </div>
                @elseif ($booking->status === 'rejected')
                    <div class="timeline-item timeline-done timeline-danger">
                        <div class="timeline-dot timeline-dot-rejected">
                            <i class="ti ti-circle-x"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Booking Tidak Disetujui</p>
                            <p class="timeline-desc">
                                Mohon maaf, booking Anda tidak dapat kami proses.
                                @if ($booking->reject_desc)
                                    <br><strong>Alasan:</strong> {{ $booking->reject_desc }}
                                @endif
                            </p>
                            <p class="timeline-date">{{ \Carbon\Carbon::parse($booking->updated_at)->translatedFormat('d F Y, H:i') }} WIB</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endisset
</div>

@endsection