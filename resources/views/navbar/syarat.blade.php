@extends('layouts.app')

@section('title', 'Syarat dan Ketentuan - NginapYuk')

@section('content')
@php $diperbarui = '11 Oktober 2026'; @endphp {{-- ubah tanggal ini setiap kali isi halaman diperbarui --}}

<div class="legal-page">

    <header class="legal-header">
        <h1>Syarat dan Ketentuan</h1>
        <p class="legal-intro">
            Selamat datang di NginapYuk. Syarat dan Ketentuan ini mengatur penggunaan layanan pemesanan villa melalui website NginapYuk. Dengan menggunakan layanan dan melakukan reservasi, pengguna dianggap telah membaca, memahami, dan menyetujui ketentuan berikut.
        </p>
    </header>

    <nav class="legal-toc" aria-label="Daftar isi">
        <p class="legal-toc-title">Daftar isi</p>
        <ol>
            <li><a href="#ketentuan-umum">Ketentuan Umum</a></li>
            <li><a href="#pemesanan">Ketentuan Pemesanan Villa</a></li>
            <li><a href="#harga-pembayaran">Ketentuan Harga dan Pembayaran</a></li>
            <li><a href="#konfirmasi">Konfirmasi Reservasi</a></li>
            <li><a href="#menginap">Ketentuan Menginap</a></li>
            <li><a href="#pembatalan">Pembatalan dan Perubahan Reservasi</a></li>
            <li><a href="#privasi">Data dan Privasi Pengguna</a></li>
            <li><a href="#tanggung-jawab">Tanggung Jawab Pengguna dan Pengelola</a></li>
            <li><a href="#perubahan">Perubahan Syarat dan Ketentuan</a></li>
            <li><a href="#persetujuan">Persetujuan</a></li>
        </ol>
    </nav>

    <section class="legal-section" id="ketentuan-umum">
        <h2>1. Ketentuan Umum</h2>
        <ol>
            <li>NginapYuk merupakan layanan berbasis website yang membantu pengguna mencari informasi dan melakukan reservasi villa.</li>
            <li>Pengguna wajib memberikan data yang benar dan lengkap selama proses pemesanan.</li>
            <li>Pengguna wajib membaca informasi properti, fasilitas, kapasitas, harga, dan aturan menginap sebelum melakukan reservasi.</li>
            <li>Penggunaan website harus sesuai dengan hukum dan peraturan yang berlaku.</li>
        </ol>
    </section>

    <section class="legal-section" id="pemesanan">
        <h2>2. Ketentuan Pemesanan Villa</h2>
        <ol>
            <li>Pengguna dapat mencari villa berdasarkan lokasi dan tanggal menginap.</li>
            <li>Pemesanan dapat dilakukan tanpa membuat akun tamu.</li>
            <li>Pengguna wajib memeriksa kembali tanggal check-in, check-out, dan rincian biaya sebelum melanjutkan pembayaran.</li>
            <li>Ketersediaan villa mengikuti kalender reservasi yang dikelola oleh NginapYuk.</li>
            <li>Penahanan tanggal selama proses pemesanan tidak selalu berarti reservasi telah dikonfirmasi. Tanggal ditahan selama 15 menit sejak pemesanan dibuat. Jika pembayaran belum diterima dalam waktu tersebut, pemesanan dibatalkan otomatis dan tanggal dibuka kembali.</li>
            <li>Reservasi dinyatakan berhasil setelah pembayaran diverifikasi dan konfirmasi diberikan melalui WhatsApp.</li>
        </ol>
    </section>

    <section class="legal-section" id="harga-pembayaran">
        <h2>3. Ketentuan Harga dan Pembayaran</h2>
        <ol>
            <li>Harga sewa ditentukan berdasarkan villa yang dipilih, durasi menginap, dan jenis hari yang dipilih.</li>
            <li>Total biaya ditampilkan sebelum pengguna menyelesaikan transaksi.</li>
            <li>Pembayaran dilakukan menggunakan metode QRIS sesuai petunjuk yang tersedia pada website.</li>
            <li>Pengguna bertanggung jawab memastikan pembayaran dilakukan sesuai nominal yang tertera.</li>
            <li>Pengguna wajib menyimpan bukti pembayaran untuk keperluan pemeriksaan transaksi apabila diperlukan.</li>
            <li>Pembayaran yang belum terverifikasi tidak dianggap sebagai reservasi yang telah dikonfirmasi.</li>
        </ol>
    </section>

    <section class="legal-section" id="konfirmasi">
        <h2>4. Konfirmasi Reservasi</h2>
        <ol>
            <li>Informasi penting mengenai pesanan disampaikan melalui WhatsApp.</li>
            <li>Pengguna wajib memastikan nomor WhatsApp yang diberikan aktif dan dapat dihubungi.</li>
            <li>Pengguna perlu memeriksa pesan konfirmasi untuk memastikan detail reservasi sudah sesuai.</li>
            <li>Jika terdapat kesalahan data atau kendala pembayaran, pengguna dapat menghubungi pengelola NginapYuk.</li>
        </ol>
    </section>

    <section class="legal-section" id="menginap">
        <h2>5. Ketentuan Menginap</h2>
        <ol>
            <li>Pengguna wajib mematuhi aturan menginap yang tercantum pada halaman detail masing-masing villa.</li>
            <li>Jumlah tamu tidak boleh melebihi kapasitas yang ditetapkan untuk villa yang dipesan.</li>
            <li>Pengguna wajib menjaga kebersihan, keamanan, dan fasilitas villa selama menginap.</li>
            <li>Pengguna bertanggung jawab atas kerusakan fasilitas yang disebabkan oleh kelalaian atau tindakan pengguna maupun rombongannya.</li>
            <li>Pengguna dilarang menggunakan villa untuk kegiatan yang melanggar hukum atau mengganggu lingkungan sekitar.</li>
            <li>Ketentuan check-in, check-out, hewan peliharaan, dan fasilitas khusus mengikuti aturan masing-masing villa.</li>
        </ol>
    </section>

    <section class="legal-section" id="pembatalan">
        <h2>6. Pembatalan dan Perubahan Reservasi</h2>
        <ol>
            <li>Permintaan pembatalan atau perubahan tanggal reservasi harus disampaikan kepada pengelola melalui kontak resmi NginapYuk.</li>
            <li>Persetujuan perubahan tanggal bergantung pada ketersediaan villa.</li>
            <li>Ketentuan pengembalian dana dan biaya pembatalan mengikuti kebijakan yang diinformasikan kepada pengguna sebelum pembayaran.</li>
            <li>Apabila villa tidak dapat digunakan karena kendala dari pihak pengelola, penyelesaian akan dikoordinasikan dengan pengguna melalui penjadwalan ulang atau pengembalian dana sesuai kesepakatan.</li>
            <li>Pengguna disarankan menghubungi pengelola sesegera mungkin apabila ingin membatalkan atau mengubah reservasi.</li>
        </ol>
    </section>

    <section class="legal-section" id="privasi">
        <h2>7. Data dan Privasi Pengguna</h2>
        <ol>
            <li>NginapYuk menggunakan data pengguna untuk memproses reservasi, memverifikasi pembayaran, dan menyampaikan informasi pesanan.</li>
            <li>Data yang dikumpulkan dibatasi pada kebutuhan layanan pemesanan dan administrasi terkait.</li>
            <li>Pengguna wajib memastikan data kontak yang diberikan benar.</li>
            <li>NginapYuk menjaga kerahasiaan data pribadi dan mengelolanya sesuai <a href="{{ route('kebijakan') }}">Kebijakan Privasi</a> serta ketentuan hukum yang berlaku.</li>
        </ol>
    </section>

    <section class="legal-section" id="tanggung-jawab">
        <h2>8. Tanggung Jawab Pengguna dan Pengelola</h2>
        <ol>
            <li>Pengguna bertanggung jawab atas kebenaran data pemesanan dan kepatuhan terhadap aturan villa.</li>
            <li>NginapYuk berupaya menyajikan informasi properti, harga, kapasitas, dan ketersediaan secara jelas dan akurat.</li>
            <li>Pengguna dapat menghubungi pengelola untuk pertanyaan mengenai villa, pemeriksaan transaksi, atau kendala reservasi.</li>
            <li>NginapYuk berupaya membantu menyelesaikan kendala pemesanan berdasarkan informasi dan bukti yang tersedia.</li>
        </ol>
    </section>

    <section class="legal-section" id="perubahan">
        <h2>9. Perubahan Syarat dan Ketentuan</h2>
        <p>NginapYuk dapat memperbarui Syarat dan Ketentuan apabila diperlukan. Perubahan akan ditampilkan pada website. Pengguna disarankan membaca ketentuan ini sebelum melakukan pemesanan.</p>
    </section>

    <section class="legal-section" id="persetujuan">
        <h2>10. Persetujuan</h2>
        <p>Dengan mencentang kotak persetujuan dan/atau melanjutkan proses reservasi di website NginapYuk, pengguna menyatakan telah membaca, memahami, dan menyetujui Syarat dan Ketentuan yang berlaku pada penyewaan villa ini.</p>
    </section>

    <p class="legal-closing">NginapYuk, Reservasi Villa yang Lebih Mudah.</p>
    <p class="legal-updated">Terakhir diperbarui: {{ $diperbarui }}</p>
</div>

@endsection