@extends('layouts.app')

@section('title', 'Tentang Kami - Wisma PLN')

@section('content')

<div class="tentang-page">

    {{-- WRAPPER — parent relative, biar header text bisa absolute & fleksibel ditransform terpisah dari box foto --}}
    <div class="tentang-hero-wrap">
        <div class="tentang-hero-box">
            <img src="{{ asset('images/forest-tentang.jpg') }}" alt="Wisma PLN" class="tentang-hero-img">
            <div class="tentang-hero-overlay"></div>
        </div>

        {{-- Header TIDAK di dalam .tentang-hero-box — elemen terpisah, diposisikan lewat CSS --}}
        <h1 class="tentang-hero-title">Tentang Wisma PLN</h1>
    </div>

    {{-- KONTEN ESSAY --}}
    <div class="tentang-content">

        <div class="tentang-section">
            <h2>Perjalanan yang Bermula dari Sebuah Kebutuhan Sederhana</h2>
            <p>
                Setiap perjalanan dinas, setiap kunjungan kerja, dan setiap momen istirahat yang layak — dimulai dari satu pertanyaan sederhana: di mana kami akan menginap? Pertanyaan inilah yang menjadi titik awal lahirnya Wisma PLN, sebuah inisiatif dari PT PLN (Persero) untuk menghadirkan tempat singgah yang nyaman, terpercaya, dan terjangkau bagi pegawai, pensiunan, maupun masyarakat umum yang membutuhkan penginapan berkualitas.
            </p>
            <p>
                Wisma PLN hadir bukan sekadar sebagai tempat menginap, melainkan sebagai bagian dari komitmen PLN untuk terus mendukung mobilitas dan kesejahteraan siapa pun yang bersinggungan dengan perjalanan — baik untuk urusan pekerjaan, keluarga, maupun sekadar melepas penat di tengah rutinitas.
            </p>
        </div>

        <div class="tentang-section">
            <h2>Kenapa Memilih Wisma PLN?</h2>
            <p>
                Di tengah banyaknya pilihan akomodasi yang tersedia saat ini, kami percaya bahwa kepercayaan dibangun dari konsistensi. Wisma PLN dikelola langsung oleh unit internal PT PLN (Persero), sehingga setiap standar kebersihan, keamanan, dan pelayanan senantiasa terjaga dan dapat dipertanggungjawabkan.
            </p>
            <p>
                Bagi pegawai dan pensiunan PLN, kami menghadirkan tarif khusus sebagai bentuk apresiasi atas dedikasi yang telah diberikan kepada perusahaan. Sementara bagi masyarakat umum, Wisma PLN tetap membuka pintunya lebar-lebar, menawarkan pengalaman menginap yang nyaman dengan harga yang wajar dan transparan — tanpa biaya tersembunyi, tanpa kejutan di akhir.
            </p>
            <p>
                Lokasi-lokasi wisma kami tersebar di berbagai destinasi strategis, memudahkan siapa pun yang sedang dalam perjalanan dinas maupun berlibur bersama keluarga. Setiap wisma dirancang dengan fasilitas yang lengkap — mulai dari ruang keluarga yang luas, dapur yang fungsional, hingga area parkir yang memadai, semuanya diselimuti suasana yang bersih dan menenangkan.
            </p>
        </div>

        <div class="tentang-section">
            <h2>Lebih dari Sekadar Tempat Menginap</h2>
            <p>
                Kami memahami bahwa setiap tamu memiliki cerita perjalanannya sendiri. Ada yang datang untuk menyelesaikan tugas kedinasan, ada yang membawa serta keluarga untuk berlibur, dan ada pula yang sekadar mencari ketenangan sejenak dari hiruk-pikuk kota. Apapun alasannya, Wisma PLN berkomitmen untuk menjadi rumah kedua yang menyambut dengan hangat.
            </p>
            <p>
                Proses pemesanan yang kami hadirkan pun dirancang sesederhana mungkin — cukup pilih wisma, tentukan tanggal, lengkapi data diri, dan unggah dokumen pendukung. Tim admin kami akan segera memverifikasi dan mengonfirmasi pemesanan Anda melalui WhatsApp, tanpa perlu menunggu lama atau melalui proses yang berbelit.
            </p>
        </div>

        <div class="tentang-stats">
            <div class="tentang-stat-item">
                <strong>5</strong>
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
            <h2>Bersama Wisma PLN, Setiap Perjalanan Punya Tempat Pulang</h2>
            <p>
                Kami percaya bahwa kenyamanan menginap adalah bagian penting dari sebuah perjalanan yang baik. Karena itu, Wisma PLN akan terus berkembang — menambah destinasi, meningkatkan fasilitas, dan menyempurnakan layanan — demi menghadirkan pengalaman terbaik bagi setiap tamu yang datang.
            </p>
            <p>
                Terima kasih telah mempercayakan perjalanan Anda kepada kami. Kami menantikan kunjungan Anda di salah satu wisma PLN, di mana pun Anda berada.
            </p>
        </div>

    </div>
</div>

@endsection