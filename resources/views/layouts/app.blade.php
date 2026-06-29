<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Wisma PLN')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/web-native.css', 'resources/js/web-native.js'])

    @stack('styles')
</head>
<body>

<nav class="nav">
    <a href="/" class="nav-brand">
        <i class="ti ti-building-community"></i> Wisma PLN
    </a>
    <div class="nav-links">
        <a href="/#wisma">Wisma</a>
        <a href="/#tentang">Tentang</a>
        <a href="/#kontak">Kontak</a>
    </div>
    <a href="/cek-booking" class="nav-btn">Cek Booking</a>
    <button class="nav-hamburger">
        <i class="ti ti-menu-2"></i>
    </button>
</nav>
<div class="nav-mobile-menu">
    <a href="/#wisma">Wisma</a>
    <a href="/#tentang">Tentang</a>
    <a href="/#kontak">Kontak</a>
    <a href="/cek-booking">Cek Booking</a>
</div>

@yield('content')

<div class="footer">
    <span style="color:#fff;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;">
        <i class="ti ti-building-community" style="color:var(--blue-accent)"></i> Wisma PLN
    </span>
    <div style="display:flex;gap:24px;">
        <a href="#" style="color:rgba(255,255,255,0.5);font-size:12px;font-weight:500;">Kebijakan privasi</a>
        <a href="#" style="color:rgba(255,255,255,0.5);font-size:12px;font-weight:500;">Syarat & ketentuan</a>
        <a href="#" style="color:rgba(255,255,255,0.5);font-size:12px;font-weight:500;">Kontak</a>
    </div>
    <span style="color:rgba(255,255,255,0.3);font-size:11px;">© {{ date('Y') }} PT PLN (Persero)</span>
</div>

@stack('scripts')
</body>
</html>