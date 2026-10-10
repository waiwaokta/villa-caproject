@extends('errors.layout')
@section('code', '500')
@section('marquee', 'SERVER BERMASALAH')
@section('title', 'Terjadi kesalahan pada server')
@section('message', 'Maaf, ada gangguan di sisi kami. Kesalahan ini sudah tercatat. Silakan coba lagi beberapa saat lagi. Jika Anda sedang membayar, cek status booking Anda sebelum mencoba ulang.')
@section('actions')
    <a href="/cek-booking" class="btn-back-home"><i class="ti ti-search"></i> Cek Booking</a> {{-- supaya tamu tidak membayar dua kali --}}
    <a href="/" class="btn-cek-status"><i class="ti ti-home"></i> Beranda</a>
@endsection