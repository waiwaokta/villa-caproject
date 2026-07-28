@extends('layouts.app')

@section('title', 'Kontak - Wisma PLN')

@section('content')

<div class="kontak-page">
    <div class="kontak-page-header">
        <h1>Hubungi Kami</h1>
        <p>Kirimkan pertanyaan anda seputar wisma atau proses booking melalui kontak yang tersedia.</p>
    </div>

    @if (session('success'))
        <div class="kontak-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="kontak-grid">

        {{-- BOX 1 — FORM EMAIL --}}
        <div class="kontak-box">
            <h2>Kirim Pesan via Email</h2>
            <p class="kontak-box-sub">Pesan anda akan langsung dihubungkan melalui tim kami.</p>

            @if ($errors->any())
                <div class="form-error" style="display:block; margin-bottom:16px;">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('kontak.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" required maxlength="255" placeholder="Nama lengkap Anda" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required maxlength="255" placeholder="email@contoh.com" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea name="description" required maxlength="2000" rows="5" placeholder="Tulis pertanyaan atau pesan Anda di sini">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="btn-submit-booking">
                    <i class="ti ti-send"></i> Kirim Email
                </button>
            </form>
        </div>

        {{-- BOX 2 — WHATSAPP --}}
        <div class="kontak-box kontak-box-whatsapp">
            <div class="kontak-wa-icon">
                <i class="ti ti-brand-whatsapp"></i>
            </div>
            <h2>Hubungi Kami melalui WhatsApp</h2>
            <p class="kontak-box-sub">Untuk respons yang lebih cepat, langsung chat kami melalui WhatsApp.</p>
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="btn-whatsapp">
                <i class="ti ti-brand-whatsapp"></i> Chat via WhatsApp
            </a>
        </div>

    </div>
</div>

@endsection