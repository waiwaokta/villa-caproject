@extends('errors.layout')
@section('code', '403')
@section('marquee', 'AKSES DITOLAK')
@section('title', 'Akses ditolak')
@section('message', 'Anda tidak memiliki akses ke halaman ini, atau tautan yang Anda buka sudah kedaluwarsa. Tautan nota dan konfirmasi hanya berlaku sementara. Gunakan kode booking Anda di halaman Cek Booking untuk membukanya kembali.')
@section('actions')
    <a href="/cek-booking" class="btn-back-home"><i class="ti ti-search"></i> Cek Booking</a> {{-- jalur pemulihan untuk link nota yang kedaluwarsa --}}
    <a href="/" class="btn-cek-status"><i class="ti ti-home"></i> Beranda</a>
@endsection