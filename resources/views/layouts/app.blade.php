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
                <img src="{{ asset('images/NginapYuk_Logo.png') }}" alt="Wisma PLN" class="nav-brand-img">
                <span class="nav-brand-text">NginapYuk</span>
            </a>

            <div class="nav-links">
                <a href="{{ route('villa.search') }}">VILLA</a>
                <a href="{{ route('tentang') }}">TENTANG</a>
                <a href="{{ route('kontak') }}">KONTAK</a>
            </div>

            <a href="/cek-booking" class="nav-btn">CEK BOOKING</a>

            <button class="nav-hamburger" type="button" aria-label="Buka menu" aria-expanded="false">
                <i class="ti ti-menu-2"></i>
            </button>

            {{-- Panel menu mobile --}}
            <div class="nav-mobile-menu">
                <a href="{{ route('villa.search') }}" class="{{ request()->routeIs('villa.*') ? 'active' : '' }}">
                    <span class="nm-icon"><i class="ti ti-home-2"></i></span>
                    <span class="nm-text">Villa</span>
                    <i class="ti ti-chevron-right nm-arrow"></i>
                </a>
                <a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'active' : '' }}">
                    <span class="nm-icon"><i class="ti ti-info-circle"></i></span>
                    <span class="nm-text">Tentang</span>
                    <i class="ti ti-chevron-right nm-arrow"></i>
                </a>
                <a href="{{ route('kontak') }}" class="{{ request()->routeIs('kontak') ? 'active' : '' }}">
                    <span class="nm-icon"><i class="ti ti-phone"></i></span>
                    <span class="nm-text">Kontak</span>
                    <i class="ti ti-chevron-right nm-arrow"></i>
                </a>

                <a href="{{ route('booking.cek') }}" class="nav-mobile-cta">
                    <i class="ti ti-calendar-search"></i>
                    <span>Cek Booking</span>
                </a>
            </div>
        </nav>

        <div class="nav-backdrop"></div>

        <main style="min-height: calc(100vh - 62px - 380px);">
            @yield('content')
        </main>

        <footer class="footer-new">
            <div class="footer-gradient-wrap">
                <div class="footer-main">
                {{-- KIRI: brand, kontak, social media --}}
                <div class="footer-col footer-col-brand">
                    <a href="/" class="footer-logo">
                        <img src="{{ asset('images/Nginapyuk_LogoWhite.png') }}" alt="Wisma PLN" class="footer-logo-img">
                        <span class="footer-logo-text">NginapYuk</span>
                    </a>

                    <div class="footer-contact-wrap">
                        <p class="footer-col-title">Kontak Kami</p>
                        <div class="footer-contact">
                            <div class="footer-address">
                                <i class="ti ti-building"></i>
                                <span>[Alamat Kantor]</span>
                            </div>
                            <div class="footer-address">
                                <i class="ti ti-phone"></i>
                                <span>[Nomor Telepon]</span>
                            </div>
                            <div class="footer-address">
                                <i class="ti ti-mail"></i>
                                <span>[Alamat Email]</span>
                            </div>
                        </div>
                    </div>

                    <div class="footer-social-wrap">
                        <p class="footer-social-label">Ikuti kami di sosial media</p>
                        <div class="footer-social">
                            <a href="https://www.instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="ti ti-brand-instagram"></i></a>
                            <a href="https://x.com" target="_blank" rel="noopener" aria-label="X"><i class="ti ti-brand-x"></i></a>
                            <a href="https://www.facebook.com/p" target="_blank" rel="noopener" aria-label="Facebook"><i class="ti ti-brand-facebook"></i></a>
                        </div>
                    </div>
                </div>

                {{-- KANAN: pilihan halaman (dibungkus supaya bisa dipindah urutannya di mobile) --}}
                <div class="footer-links">
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
            </div>
                
                {{-- BOTTOM BAR — copyright + kebijakan, warna beda --}}
                <div class="footer-bottom">
                    <span class="footer-copyright">© {{ date('Y') }} NginapYuk. Hak cipta dilindungi.</span>
                    <div class="footer-bottom-links">
                        <a href="{{ route('kebijakan') }}">Kebijakan Privasi</a>
                        <a href="{{  route('syarat') }}">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </footer>

        @stack('scripts')
    </div>
</body>
</html>