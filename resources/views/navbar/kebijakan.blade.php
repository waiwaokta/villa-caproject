@extends('layouts.app')

@section('title', 'Kebijakan Privasi - NginapYuk')

@section('content')
@php
    $diperbarui = '11 Oktober 2026'; // ubah tanggal ini setiap kali isi halaman diperbarui
    $kontakEmail = '[Alamat Email Pengelola]'; // ISI: email resmi pengelola
    $kontakWa    = '[Nomor WhatsApp Pengelola]'; // ISI: nomor WhatsApp resmi pengelola
@endphp

<div class="legal-page">

    <header class="legal-header">
        <h1>Kebijakan Privasi</h1>
    </header>

    <nav class="legal-toc" aria-label="Daftar isi">
        <p class="legal-toc-title">Daftar isi</p>
        <ol>
            <li><a href="#pendahuluan">Pendahuluan</a></li>
            <li><a href="#informasi-dikumpulkan">Informasi yang Dikumpulkan</a></li>
            <li><a href="#tujuan">Tujuan Penggunaan Informasi</a></li>
            <li><a href="#whatsapp">Pemesanan dan Komunikasi melalui WhatsApp</a></li>
            <li><a href="#pembayaran">Pembayaran</a></li>
            <li><a href="#perlindungan">Penyimpanan dan Perlindungan Informasi</a></li>
            <li><a href="#pihak-ketiga">Pembagian Informasi kepada Pihak Ketiga</a></li>
            <li><a href="#hak-pengguna">Hak Pengguna</a></li>
            <li><a href="#penghapusan">Penyimpanan dan Penghapusan Data</a></li>
            <li><a href="#perubahan">Perubahan Kebijakan Privasi</a></li>
            <li><a href="#kontak">Kontak</a></li>
        </ol>
    </nav>

    <section class="legal-section" id="pendahuluan">
        <h2>1. Pendahuluan</h2>
        <p>NginapYuk adalah website yang menyediakan layanan pencarian dan pemesanan villa, untuk membantu pengguna menemukan akomodasi sesuai kebutuhan. Privasi pengguna merupakan salah satu hal yang penting pada website. Oleh karena itu, kebijakan privasi ini disusun untuk menjelaskan jenis informasi yang dapat dikumpulkan, cara informasi tersebut digunakan, serta upaya yang dilakukan untuk menjaga keamanan informasi pengguna ketika mengakses dan menggunakan layanan NginapYuk.</p>
    </section>

    <section class="legal-section" id="informasi-dikumpulkan">
        <h2>2. Informasi yang Dikumpulkan</h2>
        <p>Pada proses penggunaan layanan, NginapYuk dapat mengumpulkan informasi yang diperlukan untuk mendukung pencarian dan pemesanan villa, antara lain:</p>
        <ol>
            <li><strong>Informasi pemesanan:</strong> nama pemesan, tanggal check-in dan check-out, serta villa yang dipilih.</li>
            <li><strong>Informasi kontak:</strong> nomor WhatsApp aktif atau nomor telepon yang digunakan untuk komunikasi terkait pemesanan.</li>
            <li><strong>Dokumen identitas:</strong> foto atau scan KTP yang diunggah pemesan saat melakukan pemesanan, digunakan untuk memverifikasi identitas pemesan.</li>
            <li><strong>Informasi transaksi:</strong> rincian biaya dan status pembayaran yang diperlukan untuk memproses serta mengonfirmasi pemesanan.</li>
            <li><strong>Informasi penggunaan website:</strong> kunjungan ke halaman villa dicatat untuk statistik minat villa tanpa menyimpan alamat IP, dengan memakai pengenal sesi yang disamarkan. Website juga memakai cookie sesi agar proses pemesanan dapat berjalan.</li>
        </ol>
    </section>

    <section class="legal-section" id="tujuan">
        <h2>3. Tujuan Penggunaan Informasi</h2>
        <p>Informasi pengguna dapat digunakan untuk tujuan berikut:</p>
        <ol>
            <li>Memproses dan mencatat pemesanan villa.</li>
            <li>Memeriksa ketersediaan villa berdasarkan tanggal menginap.</li>
            <li>Menghitung biaya pemesanan berdasarkan villa, durasi menginap, dan ketentuan harga yang berlaku.</li>
            <li>Memverifikasi identitas pemesan melalui dokumen yang diunggah.</li>
            <li>Mendukung proses pembayaran dan verifikasi status transaksi.</li>
            <li>Menyampaikan informasi, konfirmasi, atau perubahan terkait pemesanan melalui WhatsApp.</li>
            <li>Membantu pengelola memperbarui informasi villa, mengelola kalender ketersediaan, dan menghindari benturan jadwal pemesanan.</li>
            <li>Memelihara, mengevaluasi, dan meningkatkan kualitas layanan NginapYuk.</li>
        </ol>
    </section>

    <section class="legal-section" id="whatsapp">
        <h2>4. Pemesanan dan Komunikasi melalui WhatsApp</h2>
        <p>Website NginapYuk memungkinkan pengguna melakukan pemesanan tanpa harus membuat akun tamu terlebih dahulu. Pengguna tetap perlu memberikan informasi yang diperlukan agar pemesanan dapat diproses.</p>
        <p>WhatsApp dapat digunakan sebagai sarana komunikasi untuk menyampaikan informasi atau konfirmasi pemesanan. Ketika pengguna berkomunikasi melalui WhatsApp, pemrosesan informasi juga dapat tunduk pada kebijakan dan ketentuan layanan WhatsApp sebagai penyedia layanan pihak ketiga.</p>
        <p>Pengguna diharapkan memastikan bahwa informasi kontak yang diberikan benar dan dapat dihubungi untuk keperluan pemesanan.</p>
    </section>

    <section class="legal-section" id="pembayaran">
        <h2>5. Pembayaran</h2>
        <p>NginapYuk menyediakan pembayaran melalui QRIS sesuai mekanisme yang diterapkan pada website. Informasi transaksi digunakan untuk membantu proses pembayaran dan konfirmasi pemesanan.</p>
        <p>Pengguna perlu memastikan bahwa pembayaran dilakukan melalui instruksi atau mekanisme pembayaran yang ditampilkan pada proses pemesanan. Pembayaran diproses melalui penyedia layanan pembayaran pihak ketiga, saat ini Midtrans. Pemrosesan data oleh penyedia tersebut mengikuti kebijakan privasi dan ketentuan yang berlaku pada penyedia layanan terkait.</p>
        <p>NginapYuk tidak menyimpan data kartu, rekening, maupun akun e-wallet pengguna.</p>
    </section>

    <section class="legal-section" id="perlindungan">
        <h2>6. Penyimpanan dan Perlindungan Informasi</h2>
        <p>NginapYuk berupaya menjaga informasi pengguna agar tidak diakses, digunakan, diubah, atau diungkapkan tanpa kewenangan. Upaya perlindungan dapat mencakup pembatasan akses terhadap informasi pemesanan dan pengelolaan data sesuai kebutuhan operasional layanan.</p>
        <p>Foto KTP disimpan pada penyimpanan privat yang tidak dapat diakses publik melalui alamat langsung. Dokumen tersebut hanya dapat dilihat oleh pengelola yang berwenang dan oleh pemesan melalui tautan sementara pada halaman pemesanannya.</p>
        <p>Informasi pengguna hanya boleh diakses oleh pihak yang memiliki kepentingan dan kewenangan sesuai tugasnya. Meskipun demikian, tidak ada metode penyimpanan atau pengiriman data melalui sistem elektronik yang dapat dijamin sepenuhnya bebas dari risiko keamanan.</p>
    </section>

    <section class="legal-section" id="pihak-ketiga">
        <h2>7. Pembagian Informasi kepada Pihak Ketiga</h2>
        <p>NginapYuk tidak bermaksud memperjualbelikan informasi pribadi pengguna. Informasi dapat digunakan atau disampaikan kepada pihak terkait apabila diperlukan untuk:</p>
        <ol>
            <li>Memproses dan mengelola pemesanan villa.</li>
            <li>Mendukung komunikasi melalui layanan pengiriman pesan WhatsApp, saat ini Fonnte.</li>
            <li>Mendukung proses pembayaran melalui penyedia layanan pembayaran, saat ini Midtrans.</li>
            <li>Memenuhi kewajiban berdasarkan peraturan perundang-undangan yang berlaku.</li>
        </ol>
        <p>Informasi hanya dibagikan sesuai kebutuhan dan tujuan yang relevan dengan layanan, dengan tetap memperhatikan ketentuan perlindungan data pribadi yang berlaku.</p>
    </section>

    <section class="legal-section" id="hak-pengguna">
        <h2>8. Hak Pengguna</h2>
        <p>Pengguna dapat mengajukan permintaan terkait informasi pribadinya, sesuai dengan ketentuan yang berlaku, termasuk:</p>
        <ol>
            <li>Meminta informasi mengenai penggunaan data pribadi.</li>
            <li>Meminta perbaikan apabila terdapat informasi yang tidak akurat.</li>
            <li>Meminta penghapusan atau penghentian pemrosesan data apabila memenuhi ketentuan yang berlaku.</li>
            <li>Menyampaikan pertanyaan, keberatan, atau keluhan mengenai pengelolaan informasi pribadi.</li>
        </ol>
        <p>Permintaan tersebut dapat disampaikan melalui kontak resmi pengelola NginapYuk yang dicantumkan pada website.</p>
    </section>

    <section class="legal-section" id="penghapusan">
        <h2>9. Penyimpanan dan Penghapusan Data</h2>
        <p>Informasi pemesanan disimpan selama diperlukan untuk mendukung operasional layanan, pencatatan transaksi, penyelesaian kendala pemesanan, serta pemenuhan kewajiban hukum yang berlaku.</p>
        <p>Apabila informasi tidak lagi diperlukan untuk tujuan tersebut, informasi dapat dihapus atau ditangani sesuai kebijakan penyimpanan data yang diterapkan oleh pengelola NginapYuk dan ketentuan peraturan perundang-undangan.</p>
    </section>

    <section class="legal-section" id="perubahan">
        <h2>10. Perubahan Kebijakan Privasi</h2>
        <p>NginapYuk dapat memperbarui kebijakan privasi ini apabila terdapat perubahan fitur, mekanisme pemesanan, penggunaan data, atau ketentuan hukum yang berlaku. Perubahan akan dicantumkan pada halaman kebijakan privasi di website.</p>
        <p>Pengguna disarankan untuk membaca kebijakan privasi ini secara berkala agar mengetahui informasi terbaru mengenai pengelolaan data pribadi.</p>
    </section>

    <section class="legal-section" id="kontak">
        <h2>11. Kontak</h2>
        <p>Apabila pengguna memiliki pertanyaan, permintaan, atau keluhan terkait kebijakan privasi dan pengelolaan informasi pribadi, pengguna dapat menghubungi pengelola NginapYuk melalui:</p>
        <ul class="legal-contact">
            <li><span>Nama layanan</span><strong>NginapYuk</strong></li>
            <li><span>Email</span><strong>{{ $kontakEmail }}</strong></li>
            <li><span>Nomor WhatsApp</span><strong>{{ $kontakWa }}</strong></li>
            <li><span>Website</span><strong>{{ url('/') }}</strong></li>
        </ul>
    </section>

    <p class="legal-updated">Terakhir diperbarui: {{ $diperbarui }}</p>
</div>

@endsection