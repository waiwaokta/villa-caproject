@extends('errors.layout')
@section('code', '419')
@section('marquee', 'SESI BERAKHIR')
@section('title', 'Sesi halaman berakhir')
@section('message', 'Halaman ini terlalu lama terbuka sehingga sesinya berakhir. Silakan kembali dan kirim ulang formulirnya. Data yang sudah diisi mungkin perlu dimasukkan lagi.')
@section('actions')
    <a href="{{ url()->previous('/') }}" class="btn-back-home"><i class="ti ti-arrow-left"></i> Kembali ke Halaman Sebelumnya</a>
    <a href="/" class="btn-cek-status"><i class="ti ti-home"></i> Beranda</a>
@endsection