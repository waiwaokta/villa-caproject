@extends('errors.layout')
@section('code', '503')
@section('marquee', 'SEDANG PEMELIHARAAN')
@section('title', 'Sedang dalam pemeliharaan')
@section('message', 'Kami sedang melakukan pemeliharaan singkat. Silakan kembali beberapa saat lagi.')
@section('actions')
    <a href="{{ url()->current() }}" class="btn-back-home"><i class="ti ti-refresh"></i> Muat Ulang</a>
@endsection