@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan - NginapYuk')

@section('content')
<div class="nota-page" id="notaPage"
     data-expires="{{ $booking->expires_at->toIso8601String() }}"
     data-status-url="{{ $statusUrl }}"
     data-cancel-url="{{ $cancelUrl }}"
     data-pay-url="{{ $payUrl }}"
     data-qr-url="{{ $qrUrl }}"
     data-csrf="{{ csrf_token() }}">
    <div class="nota-card">
        <h1>Periksa Pesanan Anda</h1>
        <p class="nota-sub">Tanggal ditahan untuk Anda selama <strong id="countdown"></strong>. Pastikan data sudah benar sebelum membayar.</p>
        <p class="nota-code">Kode Booking: <strong>{{ $booking->bookingID }}</strong></p>

        @include('booking.detail', ['showKtp' => true])

        <div class="nota-total">
            <div>
                <span>Grand Total</span>
                <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
            </div>
            <div class="nota-pay">
                <button type="button" id="btnPay" class="btn-cek-status"><i class="ti ti-qrcode"></i> Bayar dengan QRIS</button>
                <div id="qrisBox" hidden>
                    <img id="qrisImg" alt="QRIS" width="200" hidden>
                    <div id="qrisPlaceholder" class="qris-placeholder">Membuat kode QRIS</div>
                    <p>Scan dengan aplikasi bank atau e-wallet.</p>
                    <p id="payState">Menunggu pembayaran...</p>
                </div>
            </div>
        </div>

        <button type="button" class="btn-back-home" data-leave="/villa/{{ $booking->villaID }}">
            <i class="ti ti-arrow-left"></i> Kembali
        </button>
    </div>
</div>

<div id="leaveModal" class="modal-overlay" hidden>
    <div class="modal-box">
        <h3>Batalkan booking?</h3>
        <p>Perubahan akan hilang dan booking Anda dibatalkan bila Anda meninggalkan halaman ini. Tanggal akan dilepas dan Anda perlu memesan ulang.</p>
        <div class="modal-actions">
            <button type="button" id="leaveStay" class="btn-cek-status">Lanjutkan Pembayaran</button>
            <button type="button" id="leaveGo" class="btn-back-home">Ya, Batalkan</button>
        </div>
    </div>
</div>
@endsection