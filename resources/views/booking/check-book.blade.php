@extends('layouts.app')

@section('title', 'Cek Booking - NginapYuk')

@section('content')

<div class="cekbooking-page">
    <div class="cekbooking-header">
        <h1>Cek Status Booking</h1>
        <p>Masukkan kode booking dan nomor WhatsApp yang Anda gunakan saat memesan.</p>
    </div>

    {{-- BOX PENCARIAN --}}
    <div class="cekbooking-search-box">
        <form action="{{ route('booking.cek.cari') }}" method="POST" class="cekbooking-search-form">
            @csrf
            <div class="form-group" style="margin-bottom:0; flex:1;">
                <label>Kode Booking</label>
                <input type="text" name="booking_code" required maxlength="50" placeholder="Contoh: BK-2026-ABC123" value="{{ old('booking_code') }}">
            </div>
            <div class="form-group" style="margin-bottom:0; flex:1;">
                <label>No. WhatsApp</label>
                <input type="text" name="guest_phone" required maxlength="20" placeholder="08123456789" value="{{ old('guest_phone') }}">
            </div>
            <button type="submit" class="btn-cek-booking">
                <i class="ti ti-search"></i> Cek
            </button>
        </form>
        @if ($errors->any())
            <div class="form-error" style="display:block; margin-top:14px;">
                {{ $errors->first() }}
            </div>
        @endif
    </div>

    @if (!isset($booking))
        <div class="cekbooking-faq">
            <h2>Pertanyaan yang Sering Diajukan</h2>

            <div class="faq-item">
                <p class="faq-question">Di mana saya dapat menemukan kode booking?</p>
                <p class="faq-answer">Kode booking tampil pada halaman nota setelah Anda mengirim form booking, dan tercantum pada nota PDF setelah pembayaran berhasil.</p>
            </div>

            <div class="faq-item">
                <p class="faq-question">Berapa lama batas waktu pembayaran?</p>
                <p class="faq-answer">Setelah booking dikirim, tanggal ditahan selama 15 menit untuk menyelesaikan pembayaran QRIS. Jika waktu habis, tanggal dilepas kembali dan Anda perlu memesan ulang.</p>
            </div>

            <div class="faq-item">
                <p class="faq-question">Bagaimana jika saya ingin membatalkan booking yang sudah dibayar?</p>
                <p class="faq-answer">Silakan hubungi tim kami melalui halaman kontak dengan menyebutkan kode booking. Pembatalan dan pengembalian dana ditangani langsung oleh tim kami.</p>
            </div>

            <div class="faq-item">
                <p class="faq-question">Saya kehilangan kode booking, apa yang harus dilakukan?</p>
                <p class="faq-answer">Silakan hubungi tim kami melalui halaman kontak dengan menyertakan nama dan nomor WhatsApp yang digunakan saat melakukan pemesanan.</p>
            </div>
        </div>
    @endif

    @isset($booking)
        @php
            $statusConfig = [
                'pending'   => ['label' => 'Menunggu Pembayaran',    'icon' => 'ti-clock',        'color' => 'pending'],
                'approved'  => ['label' => 'Lunas',                  'icon' => 'ti-circle-check', 'color' => 'approved'],
                'cancelled' => ['label' => 'Dibatalkan',             'icon' => 'ti-ban',          'color' => 'rejected'],
                'expired'   => ['label' => 'Waktu Pembayaran Habis', 'icon' => 'ti-clock-off',    'color' => 'rejected'],
                'rejected'  => ['label' => 'Tidak Disetujui',        'icon' => 'ti-circle-x',     'color' => 'rejected'],
            ];
            $current = $statusConfig[$state] ?? $statusConfig['pending'];
            $wib     = fn ($d) => \Carbon\Carbon::parse($d)->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB';
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
                    <span>Villa</span>
                    <strong>{{ $booking->villa->name ?? '-' }}</strong>
                </div>
                <div class="cekbooking-info-item">
                    <span>Lokasi</span>
                    <strong>{{ $booking->villa->location ?? '-' }}</strong>
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
                {{-- TITIK 1: selalu ada --}}
                <div class="timeline-item timeline-done">
                    <div class="timeline-dot">
                        <i class="ti ti-send"></i>
                    </div>
                    <div class="timeline-content">
                        <p class="timeline-title">Booking Berhasil Dibuat</p>
                        <p class="timeline-desc">Booking Anda telah tercatat dalam sistem.</p>
                        <p class="timeline-date">{{ $wib($booking->created_at) }}</p>
                    </div>
                </div>

                {{-- TITIK 2: kondisional sesuai status --}}
                @if ($state === 'pending')
                    <div class="timeline-item timeline-current">
                        <div class="timeline-dot timeline-dot-pending">
                            <i class="ti ti-clock"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Menunggu Pembayaran</p>
                            <p class="timeline-desc">Selesaikan pembayaran QRIS sebelum batas waktu agar tanggal tetap tertahan untuk Anda.</p>
                            @if ($booking->expires_at)
                                <p class="timeline-date">Batas waktu: {{ $wib($booking->expires_at) }}</p>
                            @endif
                            @if ($payUrl)
                                <a href="{{ $payUrl }}" class="btn-cek-status"><i class="ti ti-qrcode"></i> Lanjutkan Pembayaran</a>
                            @endif
                        </div>
                    </div>
                @elseif ($state === 'approved')
                    <div class="timeline-item timeline-done timeline-success">
                        <div class="timeline-dot timeline-dot-approved">
                            <i class="ti ti-circle-check"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Pembayaran Diterima</p>
                            <p class="timeline-desc">Booking Anda sudah terkonfirmasi. Terima kasih telah memilih villa kami di NginapYuk, kami tunggu kedatangan Anda. Pembatalan hanya dapat dilakukan melalui tim kami.</p>
                            <p class="timeline-date">{{ $wib($booking->paid_at ?? $booking->updated_at) }}</p>
                            @if ($receiptUrl)
                                <a href="{{ $receiptUrl }}" class="btn-cek-status"><i class="ti ti-download"></i> Unduh Nota (PDF)</a>
                            @endif
                        </div>
                    </div>
                @elseif ($state === 'expired')
                    <div class="timeline-item timeline-done timeline-danger">
                        <div class="timeline-dot timeline-dot-rejected">
                            <i class="ti ti-clock-off"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Waktu Pembayaran Habis</p>
                            <p class="timeline-desc">Pembayaran tidak diterima sebelum batas waktu, sehingga tanggal dilepas kembali. Silakan lakukan pemesanan ulang.</p>
                            <p class="timeline-date">{{ $wib($booking->expires_at) }}</p>
                        </div>
                    </div>
                @elseif ($state === 'cancelled')
                    <div class="timeline-item timeline-done timeline-danger">
                        <div class="timeline-dot timeline-dot-rejected">
                            <i class="ti ti-ban"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Booking Dibatalkan</p>
                            <p class="timeline-desc">Booking ini telah dibatalkan. Untuk pertanyaan atau pengembalian dana, hubungi tim kami melalui halaman kontak dengan menyebutkan kode booking.</p>
                            <p class="timeline-date">{{ $wib($booking->updated_at) }}</p>
                        </div>
                    </div>
                @elseif ($state === 'rejected')
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
                            <p class="timeline-date">{{ $wib($booking->updated_at) }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endisset
</div>

@endsection