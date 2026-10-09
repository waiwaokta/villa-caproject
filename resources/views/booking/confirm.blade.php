@extends('layouts.app')

@section('title', 'Booking Berhasil! - NginapYuk')

@section('content')

<div class="confirm-page">
    <div class="confirm-card">
        <div class="confirm-icon"><i class="ti ti-circle-check"></i></div>
        <h1>Pembayaran Berhasil!</h1>
        <p>Booking Anda sudah terkonfirmasi. Simpan nota ini sebagai bukti pemesanan.</p>

        <div class="confirm-code-box">
            <p class="confirm-code-label">Kode Booking Anda</p>
            <p class="confirm-code-value">{{ $booking->bookingID }}</p>
            <p class="confirm-code-hint">Simpan kode ini untuk mengecek status booking Anda kapan saja.</p>
        </div>

        @include('booking.detail', ['showKtp' => false])

        <div class="nota-total">
            <div>
                <span>Total Dibayar</span>
                <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
            </div>
            <div><span class="badge-paid">Lunas</span></div>
        </div>

        <p class="confirm-note">
            <i class="ti ti-brand-whatsapp"></i>
            Konfirmasi akan dikirim ke WhatsApp Anda. Jika belum masuk, kode booking bisa dipakai pada halaman Cek Booking.
        </p>

        <div class="confirm-actions">
            <a href="{{ $receiptUrl }}" class="btn-cek-status"><i class="ti ti-download"></i> Unduh Nota (PDF)</a>
            <a href="{{ route('booking.cek') }}" class="btn-cek-status"><i class="ti ti-search"></i> Cek Status Booking</a>
            <a href="/" class="btn-back-home"><i class="ti ti-home"></i> Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection