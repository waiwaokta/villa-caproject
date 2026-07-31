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
        <h1 class="tentang-hero-title">Tentang Wisma PLN</h1>
    </div>

    {{-- KONTEN ESSAY --}}
    <div class="tentang-content">
        <div class="tentang-section">
            <h2>Awal Mula Sebuah Kebutuhan</h2>
            <p>
                Setiap perjalanan dinas dan setiap kunjungan kerja selalu membawa satu pertanyaan yang sama. Di mana kami akan menginap. Pertanyaan sederhana ini menjadi alasan lahirnya Wisma PLN, sebuah program dari PT PLN (Persero) untuk menyediakan tempat menginap yang nyaman dan terpercaya bagi pegawai, pensiunan, serta masyarakat umum.
            </p>
            <p>
                Wisma PLN bukan sekadar tempat tidur untuk semalam. Program ini merupakan bagian dari perhatian PLN terhadap kenyamanan siapa saja yang sedang dalam perjalanan, baik untuk urusan pekerjaan maupun untuk beristirahat bersama keluarga.
            </p>
        </div>

        <div class="tentang-section">
            <h2>Alasan Memilih Wisma PLN</h2>
            <p>
                Banyak pilihan penginapan tersedia saat ini. Namun kepercayaan tidak muncul begitu saja. Kepercayaan dibangun dari konsistensi. Wisma PLN dikelola langsung oleh unit internal PT PLN (Persero) sehingga standar kebersihan, keamanan, dan pelayanan selalu terjaga.
            </p>
            <p>
                Bagi pegawai dan pensiunan PLN, kami menyediakan tarif khusus sebagai bentuk penghargaan atas kontribusi yang telah diberikan kepada perusahaan. Bagi masyarakat umum, Wisma PLN tetap terbuka dengan harga yang wajar dan jelas sejak awal, tanpa biaya tambahan yang tidak terduga.
            </p>
            <p>
                Wisma kami tersebar di beberapa lokasi strategis sehingga memudahkan perjalanan dinas maupun liburan keluarga. Setiap wisma dilengkapi fasilitas yang memadai, mulai dari ruang keluarga yang luas, dapur yang berfungsi baik, hingga area parkir yang cukup. Semua dijaga dalam kondisi bersih dan nyaman untuk ditinggali.
            </p>
        </div>

        <div class="tentang-section">
            <h2>Lebih dari Sekadar Menginap</h2>
            <p>
                Kami memahami bahwa setiap tamu memiliki tujuan yang berbeda. Ada yang datang untuk menyelesaikan tugas kantor, ada yang membawa keluarga untuk berlibur, dan ada pula yang hanya membutuhkan waktu istirahat setelah perjalanan panjang. Apa pun tujuannya, Wisma PLN berusaha menjadi tempat yang menyambut dengan baik.
            </p>
            <p>
                Proses pemesanan dibuat sesederhana mungkin. Cukup pilih wisma, tentukan tanggal, lengkapi data diri, dan unggah dokumen yang diperlukan. Tim kami akan memverifikasi dan mengirim konfirmasi melalui WhatsApp tanpa menunggu waktu yang lama.
            </p>
        </div>

        <div class="tentang-stats">
            <div class="tentang-stat-item">
                <strong>6</strong>
                <span>Wisma Tersedia</span>
            </div>
            <div class="tentang-stat-item">
                <strong>2x</strong>
                <span>Tarif Lebih Hemat</span>
            </div>
            <div class="tentang-stat-item">
                <strong>100%</strong>
                <span>Dikelola Resmi PLN</span>
            </div>
        </div>

        <div class="tentang-section tentang-section-closing">
            <h2>Setiap Perjalanan Punya Tempat untuk Kembali</h2>
            <p>
                Kenyamanan menginap adalah bagian penting dari sebuah perjalanan yang baik. Karena itu, Wisma PLN akan terus berkembang. Kami menambah destinasi, memperbaiki fasilitas, dan meningkatkan pelayanan agar setiap tamu mendapatkan pengalaman terbaik.
            </p>
            <p>
                Terima kasih telah mempercayakan perjalanan Anda kepada kami. Kami menantikan kedatangan Anda di salah satu wisma PLN, di mana pun Anda berada.
            </p>
        </div>

    </div>
</div>

@endsection