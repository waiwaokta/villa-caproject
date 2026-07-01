@extends('layouts.app')

@section('title', 'Booking Berhasil - Wisma PLN')

@section('content')

<div class="confirm-page">
    <div class="confirm-card">
        <div class="confirm-icon">
            <i class="ti ti-circle-check"></i>
        </div>
        <h1>Booking Berhasil Dikirim!</h1>
        <p>Terima kasih telah melakukan booking. Tim kami akan segera memverifikasi data dan dokumen Anda.</p>
        <p class="confirm-note">
            <i class="ti ti-brand-whatsapp"></i>
            Konfirmasi status booking (disetujui/ditolak) akan dikirimkan melalui WhatsApp ke nomor yang Anda daftarkan.
        </p>
        <a href="/" class="btn-back-home">
            <i class="ti ti-home"></i> Kembali ke Beranda
        </a>
    </div>
</div>

@endsection