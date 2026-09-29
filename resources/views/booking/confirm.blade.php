@extends('layouts.app')

@section('title', 'Booking Berhasil - NginapYuk')

@section('content')

<div class="confirm-page">
    <div class="confirm-card">
        <div class="confirm-icon">
            <i class="ti ti-circle-check"></i>
        </div>
        <h1>Booking Berhasil Dikirim!</h1>
        <p>Terima kasih telah melakukan booking. Tim kami akan segera memverifikasi data dan dokumen Anda.</p>

        <div class="confirm-code-box">
            <p class="confirm-code-label">Kode Booking Anda</p>
            <p class="confirm-code-value">{{ $booking->bookingID }}</p>
            <p class="confirm-code-hint">Simpan kode ini untuk mengecek status booking Anda kapan saja.</p>
        </div>
w
        <p class="confirm-note">
            <i class="ti ti-brand-whatsapp"></i>
            Konfirmasi status booking akan dikirimkan melalui WhatsApp ke nomor yang Anda daftarkan atau anda bisa melalukan Cek Status Booking melalui Web.
        </p>

        <div class="confirm-actions">
            <a href="{{ route('booking.cek') }}" class="btn-cek-status">
                <i class="ti ti-search"></i> Cek Status Booking
            </a>
            <a href="/" class="btn-back-home">
                <i class="ti ti-home"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

@endsection