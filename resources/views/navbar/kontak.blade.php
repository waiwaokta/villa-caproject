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
    <div class="kontak-faq">
        <h2>Pertanyaan yang Sering Diajukan</h2>

        <div class="faq-item">
            <p class="faq-question">Jam berapa tim kami merespons pesan?</p>
            <p class="faq-answer">Tim kami merespons pada jam kerja, Senin sampai Jumat pukul 08.00 sampai 17.00 WIB. Pesan yang masuk di luar jam tersebut akan diproses pada hari kerja berikutnya.</p>
        </div>

        <div class="faq-item">
            <p class="faq-question">Apakah bisa melakukan booking melalui telepon?</p>
            <p class="faq-answer">Saat ini seluruh proses booking dilakukan melalui form pada situs kami. Jika membutuhkan bantuan mengisi form, silakan hubungi kami melalui WhatsApp dan tim akan membantu proses pemesanan.</p>
        </div>

        <div class="faq-item">
            <p class="faq-question">Bagaimana proses pengembalian dana jika booking dibatalkan?</p>
            <p class="faq-answer">Kebijakan pengembalian dana disesuaikan dengan ketentuan yang berlaku pada saat pemesanan. Silakan hubungi tim kami melalui email atau WhatsApp untuk informasi lebih lanjut mengenai kasus pembatalan yang dialami.</p>
        </div>

        <div class="faq-item">
            <p class="faq-question">Berapa lama waktu yang dibutuhkan untuk mendapat balasan email?</p>
            <p class="faq-answer">Balasan email umumnya dikirim dalam waktu satu hingga dua hari kerja. Untuk respons yang lebih cepat, kami menyarankan menghubungi kami melalui WhatsApp.</p>
        </div>
    </div>
</div>

@endsection