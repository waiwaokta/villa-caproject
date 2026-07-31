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

        {{-- BOX 2 — WHATSAPP --}}
        <div class="kontak-single-wrap">
            <div class="kontak-box kontak-box-whatsapp">
                <div class="kontak-wa-icon">
                    <i class="ti ti-brand-whatsapp"></i>
                </div>
                <h2>Hubungi Kami melalui WhatsApp</h2>
                <p class="kontak-box-sub">Untuk respons yang lebih cepat, langsung chat kami melalui WhatsApp.</p>
                <a href="https://wa.me/6281226387922" target="_blank" rel="noopener" class="btn-whatsapp">
                    <i class="ti ti-brand-whatsapp"></i> Chat via WhatsApp
                </a>
            </div>
        </div>

    <div class="kontak-faq">
        <h2>Pertanyaan yang Sering Diajukan</h2>

        <div class="faq-item">
            <p class="faq-question">Apakah bisa tanya-tanya dulu sebelum melakukan booking?</p>
            <p class="faq-answer">Tentu bisa. Anda dapat menghubungi kami melalui WhatsApp untuk menanyakan ketersediaan wisma, fasilitas, harga, atau hal lain sebelum memutuskan untuk booking. Tim kami akan membantu memberikan informasi yang dibutuhkan.</p>
        </div>

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
            <p class="faq-answer">Kebijakan pengembalian dana disesuaikan dengan ketentuan yang berlaku pada saat pemesanan. Silakan hubungi tim kami melalui WhatsApp untuk informasi lebih lanjut mengenai kasus pembatalan yang dialami.</p>
        </div>

    </div>
</div>

@endsection