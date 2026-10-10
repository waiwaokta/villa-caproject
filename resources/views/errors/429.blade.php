@extends('errors.layout')
@php $retry = method_exists($exception, 'getHeaders') ? ($exception->getHeaders()['Retry-After'] ?? null) : null; @endphp {{-- detik tunggu dari header Retry-After --}}
@section('code', '429')
@section('marquee', 'TERLALU CEPAT')
@section('title', 'Terlalu banyak percobaan')
@section('message', 'Anda mengirim terlalu banyak permintaan dalam waktu singkat. Mohon tunggu sebentar, lalu coba lagi.')
@if ($retry)
    @section('extra')
        <p class="err-hint">Coba lagi dalam sekitar {{ (int) $retry }} detik.</p>
    @endsection
@endif