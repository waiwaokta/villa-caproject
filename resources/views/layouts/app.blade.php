<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NginapYuk')</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    {{-- Open Graph Meta (preview pas link di-share ke WA/Telegram/dll) --}}
    <meta property="og:title" content="@yield('title', 'NginapYuk')">
    <meta property="og:description" content="Penginapan resmi PT PLN (Persero), hadir untuk mendukung kenyamanan perjalanan dinas maupun liburan Anda.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/web-native.css', 'resources/js/web-native.js'])

    @stack('styles')
</head>

<body>
    <div class="site">
        <nav class="nav">
            <a href="/" class="nav-brand">
                <img src="{{ asset('images/Logo_PLN_nontext.png') }}" alt="Wisma PLN" class="nav-brand-img"> {{-- DIUBAH — class khusus nav, bukan pakai footer-logo-img --}}
                <span class="nav-brand-text">NginapYuk</span> {{-- DITAMBAHKAN — bungkus teks --}}
            </a>
            <div class="nav-links">
                <a href="{{ route('villa.search') }}">VILLA</a>
                <a href="{{ route('tentang') }}">TENTANG</a>
                <a href="{{ route('kontak') }}">KONTAK</a>
            </div>
            <a href="/cek-booking" class="nav-btn">CEK BOOKING</a>
            <button class="nav-hamburger">
                <i class="ti ti-menu-2"></i>
            </button>
        </nav>
        <div class="nav-mobile-menu">
            <a href="{{ route('villa.search') }}">VILLA</a>
            <a href="{{ route('tentang') }}">TENTANG</a>
            <a href="{{ route('kontak') }}">KONTAK</a>
            <a href="/cek-booking">CEK BOOKING</a>
        </div>

        <main style="min-height: calc(100vh - 62px - 380px);">
            @yield('content')
        </main>

        <footer class="footer-new">
            <div class="footer-gradient-wrap">
                <div class="footer-main">

                    {{-- KIRI — Brand, deskripsi, social media --}}
                    <div class="footer-col footer-col-brand">
                        <a href="/" class="footer-logo">
                            <img src="{{ asset('images/Logo_PLN_nontext.png') }}" alt="Wisma PLN" class="footer-logo-img">
                            <span class="footer-logo-text">NginapYuk</span>
                        </a>
                        <p class="footer-desc">
                            Penginapan resmi PT PLN (Persero), hadir untuk mendukung kenyamanan perjalanan dinas maupun liburan Anda.
                        </p>
                        <div class="footer-address">
                            <i class="ti ti-map-pin"></i>
                            <span>Jl. Embong Trengguli No.19-21, Embong Kaliasin, Kec. Genteng, Surabaya, Jawa Timur 60271</span>
                        </div>

                        <p class="footer-social-label">Ikuti kami di sosial media</p>
                        <div class="footer-social">
                            <a href="https://www.instagram.com/plndistribusijatim/" target="_blank" rel="noopener" aria-label="Instagram"><i class="ti ti-brand-instagram"></i></a>
                            <a href="https://x.com/plndisjatim" target="_blank" rel="noopener" aria-label="X"><i class="ti ti-brand-x"></i></a>
                            <a href="https://www.facebook.com/p/PLN-Distribusi-Jawa-Timur-100064407082737/?locale=id_ID" target="_blank" rel="noopener" aria-label="Facebook"><i class="ti ti-brand-facebook"></i></a>
                        </div>
                    </div>

                    {{-- TENGAH-KANAN — 2 kolom link --}}
                    <div class="footer-col">
                        <p class="footer-col-title">VILLA</p>
                        <a href="{{ route('villa.search') }}">Villa</a>
                        <a href="{{ route('booking.cek') }}">Cek Booking</a>
                    </div>

                    <div class="footer-col">
                        <p class="footer-col-title">PERUSAHAAN</p>
                        <a href="{{ route('tentang') }}">Tentang</a>
                        <a href="{{ route('kontak') }}">Kontak</a>
                    </div>
                </div>
                
                {{-- BOTTOM BAR — copyright + kebijakan, warna beda --}}
                <div class="footer-bottom">
                    <span class="footer-copyright">© {{ date('Y') }} PT PLN (Persero). Hak cipta dilindungi.</span>
                    <div class="footer-bottom-links">
                        <a href="#">Kebijakan Privasi</a>
                        <a href="#">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </footer>

        @stack('scripts')
    </div>
</body>
</html>