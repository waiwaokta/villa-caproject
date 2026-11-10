@extends('layouts.app')

@section('title', 'Tentang Kami - Wisma PLN')

@section('content')

<div class="tentang-page">

    {{-- WRAPPER — parent relative, biar header text bisa absolute & fleksibel ditransform terpisah dari box foto --}}
    <div class="tentang-hero-wrap">
        <div class="tentang-hero-box">
            <img src="{{ asset('images/forest-tentang.webp') }}" alt="Wisma PLN" class="tentang-hero-img">
            <div class="tentang-hero-overlay"></div>
        </div>

        {{-- Header TIDAK di dalam .tentang-hero-box — elemen terpisah, diposisikan lewat CSS --}}
        <h1 class="tentang-hero-title">Tentang NginapYuk</h1>
    </div>

    {{-- KONTEN ESSAY --}}
    <div class="tentang-content">
        <div class="tentang-section">
            <h2>Reservasi Villa yang Menyenangkan</h2>
            <p>
                NginapYuk adalah perusahaan yang berfokus pada layanan pemesanan villa berbasis website. Kami membantu Anda mencari tempat menginap, memeriksa informasi properti, memilih tanggal, dan melakukan reservasi melalui proses yang mudah diikuti.
            </p>
            <p>
                Bagi kami, kenyamanan perjalanan dimulai dari informasi yang jelas. Karena itu, NginapYuk menyajikan foto, fasilitas, kapasitas, lokasi, dan rincian harga agar Anda dapat mempertimbangkan pilihan sebelum memesan.
            </p>
        </div>

        <div class="tentang-section">
            <h2>Apa yang Kami Tawarkan</h2>
            <p>
                NginapYuk menyediakan pencarian villa berdasarkan lokasi, tanggal menginap, dan jumlah tamu. Setiap halaman detail membantu Anda mengenali properti serta memahami aturan menginapnya.            
            </p>
            <p>
                Pemesanan dapat dilakukan tanpa membuat akun tamu. Setelah memilih villa, Anda mengisi data yang diperlukan, memeriksa rincian biaya, dan mengikuti pembayaran QRIS. Informasi penting mengenai pesanan disampaikan melalui WhatsApp.            
            </p>
            <p>
                Harga menyesuaikan villa, durasi menginap, dan jenis hari yang dipilih. Total pembayaran ditampilkan sebelum transaksi diselesaikan.            
            </p>
        </div>

        <div class="tentang-section">
            <h2>Mengapa NginapYuk Dikembangkan</h2>
            <p>
                Proses pemesanan melalui percakapan manual dapat membuat informasi harga, ketersediaan, dan status pembayaran tersebar di berbagai tempat.            
            </p>
            <p>
                NginapYuk dikembangkan untuk menyatukan proses tersebut. Tamu dapat menemukan informasi dan mengajukan reservasi melalui website, sementara pengelola dapat mengatur properti, kalender, serta pesanan secara lebih teratur.
            </p>
            <p>
                Kalender yang dikelola secara terpusat dan penahanan tanggal selama proses pemesanan membantu mengurangi risiko jadwal yang bertabrakan.
            </p>
        </div>

        <div class="tentang-section">
            <h2>Tujuan Kami</h2>
            <p>
                Kami ingin membantu tamu memilih villa berdasarkan informasi yang dapat diperiksa dan memahami setiap tahap pemesanannya.
            </p>
            <p>
                Kami juga ingin memudahkan pengelola menjaga informasi properti, mengatur ketersediaan, dan menangani reservasi, sehingga layanan kepada tamu dapat diberikan dengan lebih konsisten.
            </p>
        </div>

        <div class="tentang-stats">
            <div class="tentang-stat-item">
                <strong>10+</strong>
                <span>Villa Tersedia</span>
            </div>
            <div class="tentang-stat-item">
                <strong>2x</strong>
                <span>Tarif Lebih Terjangkau</span>
            </div>
            <div class="tentang-stat-item">
                <strong>100%</strong>
                <span>Di</span>
            </div>
        </div>

        <div class="tentang-section tentang-section-closing">
            <h2>Setiap Perjalanan Punya Tempat untuk Kembali</h2>
            <p>
                Kenyamanan menginap adalah bagian penting dari sebuah perjalanan yang baik. Karena itu, NginapYuk akan terus berkembang. Kami menambah destinasi, memperbaiki fasilitas, dan meningkatkan pelayanan agar setiap tamu mendapatkan pengalaman terbaik.
            </p>
            <p>
                Terima kasih telah mempercayakan perjalanan Anda kepada kami.
            </p>
        </div>

    </div>
</div>

@endsection