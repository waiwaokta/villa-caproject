<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('code') - @yield('title') | NginapYuk</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"> {{-- 800 untuk teks raksasa --}}
    @vite(['resources/css/web-native.css'])
</head>
<body>
    @php
        $code   = trim($__env->yieldContent('code')); // teks baris 1
        $phrase = trim($__env->yieldContent('marquee', 'TERJADI KESALAHAN')); // teks baris 2
    @endphp

    <div class="err-site">
        <nav class="nav">
            <a href="/" class="nav-brand"><span class="nav-brand-text">NginapYuk</span></a> {{-- teks saja, logo menyusul dari tim --}}
        </nav>

        <main class="err-stage">
            <div class="err-band" aria-hidden="true">
                <div class="err-track">
                    @for ($g = 0; $g < 2; $g++) {{-- dua grup identik supaya putarannya mulus --}}
                        <div class="err-group">
                            @for ($i = 0; $i < 6; $i++)<span>{{ $code }}</span>@endfor
                        </div>
                    @endfor
                </div>
            </div>

            <div class="err-band" aria-hidden="true">
                <div class="err-track err-track-rev"> {{-- arah berlawanan dengan baris 1 --}}
                    @for ($g = 0; $g < 2; $g++)
                        <div class="err-group">
                            @for ($i = 0; $i < 3; $i++)<span>{{ $phrase }}</span>@endfor
                        </div>
                    @endfor
                </div>
            </div>

            <div class="err-text">
                <h1 class="err-title">@yield('title')</h1>
                <p class="err-desc">@yield('message')</p>

                @yield('extra') {{-- slot opsional, dipakai halaman 429 --}}

                <div class="err-actions">
                    @hasSection('actions')
                        @yield('actions')
                    @else
                        <a href="/" class="btn-back-home"><i class="ti ti-chevron-left"></i> Kembali ke Beranda</a>
                        <a href="/cari" class="btn-cek-status"><i class="ti ti-search"></i> Cari Villa</a>
                    @endif
                </div>
            </div>
        </main>

        <footer class="err-footer">© {{ date('Y') }} NginapYuk</footer>
    </div>
</body>
</html>