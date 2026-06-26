<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Wisma PLN')</title>

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">

    <!-- Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #FAFAF8;
            --cream2: #F2F2EF;
            --blue: #185FA5;
            --blue-dark: #0C447C;
            --blue-deeper: #042C53;
            --blue-deepest: #021a36;
            --blue-light: #85B7EB;
            --blue-lighter: #B5D4F4;
            --blue-accent: #00A3AD;
            --text: #1a1a18;
            --text-secondary: #6b6b68;
            --border: #e8e8e4;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }

        /* NAV */
        .nav {
            background: rgba(12, 68, 124, 0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 14px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .nav-brand {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.01em;
        }
        .nav-brand i { color: var(--blue-accent); font-size: 20px; }
        .nav-links { display: flex; gap: 28px; }
        .nav-links a {
            color: rgba(255,255,255,0.65);
            font-size: 13px;
            font-weight: 500;
            transition: color .2s;
            letter-spacing: 0.01em;
        }
        .nav-links a:hover { color: #fff; }
        .nav-btn {
            background: var(--blue-accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: opacity .2s;
            letter-spacing: 0.01em;
        }
        .nav-btn:hover { opacity: 0.88; }

        /* FOOTER */
        .footer {
            background: var(--blue-deepest);
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        /* DIVIDER */
        .divider { height: 1px; background: var(--border); margin: 0 40px; }

        /* SECTION */
        .section { padding: 56px 40px; background: var(--cream); }
        .section-header { margin-bottom: 32px; }
        .section-header h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
            color: var(--text);
        }
        .section-header p { font-size: 14px; color: var(--text-secondary); }
    </style>

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
</nav>

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