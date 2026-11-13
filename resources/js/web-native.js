document.addEventListener('DOMContentLoaded', () => {
    initSliderVilla();
    initSearchFilter();
    initCapacityFilter();
    initNavHamburger();
    initGallery();
    initLightbox();
    initAvailabilityCalendar();
    initBookingForm();
    initStatTicker();
    initVillaCardClick();
    initVillaSearchDateGuard();
    initTickerCardVilla();
    initNotaPage();
    initLoadingButtons();
});

    function getTodayLocal() {
        const now = new Date();
        const year  = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day   = String(now.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

// ============================================
// TICKER STATISTIK BOOKING — beranda
// ============================================
function initStatTicker() {
    const track = document.getElementById('statTickerTrack');
    if (!track) return; 

    const original = track.innerHTML;
    track.innerHTML = original.repeat(4);
}

    // ============================================
    // SLIDER VILLA — beranda
    // ============================================
    function initSliderVilla() {
    const fwWrap = document.getElementById('fwWrap');
    if (!fwWrap) return; // halaman ini tidak punya slider, skip

    const fwTrack  = document.getElementById('fwTrack');
    const fwDotsEl = document.getElementById('fwDots');
    const total    = parseInt(fwWrap.dataset.total, 10);
    let cur = 0, startX = 0, isDragging = false, accumX = 0, scrollLocked = false;

    for (let i = 0; i < total; i++) {
        const d = document.createElement('div');
        d.className = 'fw-dot' + (i === 0 ? ' active' : '');
        d.onclick = () => goTo(i);
        fwDotsEl.appendChild(d);
    }

    function goTo(n) {
        cur = Math.max(0, Math.min(n, total - 1));
        fwTrack.style.transform = `translateX(-${cur * 100}%)`;
        document.getElementById('fwCounter').textContent = `${cur + 1} / ${total}`;
        document.querySelectorAll('.fw-dot').forEach((d, i) => d.classList.toggle('active', i === cur));
    }

    fwWrap.addEventListener('wheel', e => {
        const isH = Math.abs(e.deltaX) > Math.abs(e.deltaY);
        if (!isH) return;
        e.preventDefault();
        if (scrollLocked) return;
        accumX += e.deltaX;
        if (Math.abs(accumX) > 50) {
            goTo(accumX > 0 ? cur + 1 : cur - 1);
            accumX = 0;
            scrollLocked = true;
            setTimeout(() => { scrollLocked = false; }, 500);
        }
    }, { passive: false });

    fwWrap.addEventListener('touchstart', e => { startX = e.touches[0].clientX; isDragging = true; }, { passive: true });
    fwWrap.addEventListener('touchend', e => {
        if (!isDragging) return;
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(diff > 0 ? cur + 1 : cur - 1);
        isDragging = false;
    });

    fwWrap.addEventListener('mousedown', e => { startX = e.clientX; isDragging = true; e.preventDefault(); });
    document.addEventListener('mouseup', e => {
        if (!isDragging) return;
        const diff = startX - e.clientX;
        if (Math.abs(diff) > 40) goTo(diff > 0 ? cur + 1 : cur - 1);
        isDragging = false;
    });

    setInterval(() => goTo(cur + 1 < total ? cur + 1 : 0), 5500);
}

// ============================================
// SEARCH FILTER — beranda
// ============================================
function initSearchFilter() {
    const searchBtn = document.querySelector('.search-btn');
    if (!searchBtn) return;

    const checkinInput  = document.getElementById('filter-checkin');
    const checkoutInput = document.getElementById('filter-checkout');

    function syncCheckoutMin() { // function terpisah biar bisa dipanggil langsung di awal juga
        if (!checkinInput.value) return;
        const nextDay = new Date(checkinInput.value + 'T00:00:00');
        nextDay.setDate(nextDay.getDate() + 1);
        const y = nextDay.getFullYear();
        const m = String(nextDay.getMonth() + 1).padStart(2, '0');
        const d = String(nextDay.getDate()).padStart(2, '0');
        const minCheckout = `${y}-${m}-${d}`;
        checkoutInput.min = minCheckout;
        if (checkoutInput.value && checkoutInput.value < minCheckout) { // kalau value sekarang udah invalid, reset otomatis
            checkoutInput.value = minCheckout;
        }
    }

    if (checkinInput && !checkinInput.value) {
        checkinInput.value = getTodayLocal();
    }
    if (checkoutInput && !checkoutInput.value) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const y = tomorrow.getFullYear();
        const m = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const d = String(tomorrow.getDate()).padStart(2, '0');
        checkoutInput.value = `${y}-${m}-${d}`;
    }
    if (checkinInput && checkoutInput) {
        syncCheckoutMin(); // diubah — panggil function baru, bukan inline lagi
    }

    window.doSearch = function () {
        setButtonLoading(searchBtn, true, 'Mencari...');
        const lokasi   = document.getElementById('filter-lokasi').value;
        const checkin  = document.getElementById('filter-checkin').value;
        const checkout = document.getElementById('filter-checkout').value;
        const params   = new URLSearchParams({
            lokasi,
            check_in: checkin,
            check_out: checkout
        });
        window.location.href = '/cari?' + params.toString();
    };

    if (checkinInput) {
        checkinInput.addEventListener('change', syncCheckoutMin); // diubah — pakai function baru, bukan inline
    }
}

// ============================================
// VALIDASI TANGGAL — form cari villa (/cari)
// ============================================
function initVillaSearchDateGuard() {
    const form = document.querySelector('.villa-box-standalone');
    if (!form) return; // halaman ini tidak punya form cari villa, skip

    const checkinInput  = form.querySelector('[name="check_in"]');
    const checkoutInput = form.querySelector('[name="check_out"]');
    if (!checkinInput || !checkoutInput) return;

    function syncMin() {
        if (!checkinInput.value) return;
        const nextDay = new Date(checkinInput.value + 'T00:00:00');
        nextDay.setDate(nextDay.getDate() + 1);
        const y = nextDay.getFullYear();
        const m = String(nextDay.getMonth() + 1).padStart(2, '0');
        const d = String(nextDay.getDate()).padStart(2, '0');
        const minCheckout = `${y}-${m}-${d}`;
        checkoutInput.min = minCheckout;
        if (checkoutInput.value && checkoutInput.value < minCheckout) {
            checkoutInput.value = minCheckout;
        }
    }

    syncMin(); // jalankan sekali di awal, karena value sudah terisi dari controller
    checkinInput.addEventListener('change', syncMin);
}

// ============================================
// KAPASITAS — beranda dan /cari, tersimpan sementara selama tab terbuka
// ============================================
function initCapacityFilter() {
    const input = document.getElementById('filter-capacity');
    if (!input) return;

    const KEY = 'guest_capacity';
    const store = {
        get() { try { return sessionStorage.getItem(KEY) || ''; } catch (e) { return ''; } },
        set(v) { try { v ? sessionStorage.setItem(KEY, v) : sessionStorage.removeItem(KEY); } catch (e) {} }
    };

    const saved = store.get();

    if (input.value) {
        // nilai sudah ada dari URL atau server, simpan
        store.set(input.value);
    } else if (saved) {
        const isSearchPage = document.querySelector('.villa-box-standalone');
        const params = new URLSearchParams(window.location.search);

        if (isSearchPage && !params.has('capacity')) {
            // masuk /cari lewat menu: tambahkan capacity ke URL supaya hasilnya ikut terfilter
            params.set('capacity', saved);
            window.location.replace(window.location.pathname + '?' + params.toString());
            return;
        }
        if (!isSearchPage) input.value = saved; // beranda: isi kolom saja
    }

    // cegah karakter yang bukan angka bulat
    input.addEventListener('keydown', e => {
        if (['e', 'E', '+', '-', '.', ','].includes(e.key)) e.preventDefault();
    });

    input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').slice(0, 3);
        store.set(input.value); // kolom dikosongkan berarti simpanan ikut terhapus
    });

    input.addEventListener('blur', () => {
        if (input.value !== '' && parseInt(input.value, 10) < 1) {
            input.value = '';
            store.set('');
        }
    });
}

// ============================================
// NAV HAMBURGER — mobile menu, semua halaman
// ============================================
function initNavHamburger() {
    const hamburger = document.querySelector('.nav-hamburger');
    const mobileMenu = document.querySelector('.nav-mobile-menu');
    const backdrop = document.querySelector('.nav-backdrop');
    if (!hamburger || !mobileMenu || !backdrop) return;

    const icon = hamburger.querySelector('i');

    function setMenu(open) {
        mobileMenu.classList.toggle('open', open);
        backdrop.classList.toggle('open', open);
        hamburger.classList.toggle('active', open);
        hamburger.setAttribute('aria-expanded', open);
        icon.className = open ? 'ti ti-x' : 'ti ti-menu-2';
    }

    hamburger.addEventListener('click', () => setMenu(!mobileMenu.classList.contains('open')));
    backdrop.addEventListener('click', () => setMenu(false));
    mobileMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });
    window.addEventListener('resize', () => { if (window.innerWidth > 768) setMenu(false); });
}

// ============================================
// GALERI FOTO — detail villa
// ============================================
function initGallery() {
    const thumbs = document.querySelectorAll('.gallery-thumb');
    const mainImg = document.getElementById('galleryMainImg');
    if (!thumbs.length || !mainImg) return;

    thumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            mainImg.src = thumb.dataset.full;
            thumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        });
    });
}

// ============================================
// KALENDER AVAILABILITY — detail villa
// ============================================
function initAvailabilityCalendar() {
    const wrap = document.getElementById('calendarWrap');
    if (!wrap) return;

    const occupiedDates  = JSON.parse(wrap.dataset.occupied || '[]');
    const todayStr       = getTodayLocal();
    const now            = new Date();
    const monthSelect    = document.getElementById('calMonthSelect');
    const yearSelect     = document.getElementById('calYearSelect');

    const monthNames = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];
    const dayLabels = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

    let currentYear  = now.getFullYear();
    let currentMonth = now.getMonth();

    // Generate pilihan tahun — dari tahun ini sampai 5 tahun ke depan
    function populateYears() {
        yearSelect.innerHTML = '';
        for (let y = now.getFullYear(); y <= now.getFullYear() + 5; y++) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            yearSelect.appendChild(opt);
        }
    }

    function syncSelects() {
        monthSelect.value = currentMonth;
        yearSelect.value  = currentYear;

        // Disable bulan yang sudah lewat kalau tahun sekarang
        Array.from(monthSelect.options).forEach(opt => {
            const m = parseInt(opt.value);
            const y = parseInt(yearSelect.value);
            opt.disabled = (y === now.getFullYear() && m < now.getMonth());
        });
    }

    function renderCalendar() {
        const year           = currentYear;
        const month          = currentMonth;
        const daysInMonth    = new Date(year, month + 1, 0).getDate();
        const firstDayOfWeek = new Date(year, month, 1).getDay();

        // Disable tombol prev kalau sudah di bulan ini
        const isCurrentMonth = year === now.getFullYear() && month === now.getMonth();
        document.getElementById('calPrev').disabled = isCurrentMonth;

        syncSelects();

        let html = '<div class="cal-grid">';
        dayLabels.forEach(label => {
            html += `<div class="cal-day-label">${label}</div>`;
        });
        for (let i = 0; i < firstDayOfWeek; i++) {
            html += `<div class="cal-day cal-day-empty"></div>`;
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr    = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const isToday    = dateStr === todayStr;
            const isPast     = dateStr < todayStr;
            const isOccupied = occupiedDates.includes(dateStr);

            let cls = 'cal-day ';
            if (isOccupied && !isPast) cls += 'cal-day-occupied' + (isToday ? ' cal-day-is-today' : '');
            else if (isToday)          cls += 'cal-day-today';
            else if (isPast)           cls += 'cal-day-past';
            else                       cls += 'cal-day-available';

            html += `<div class="${cls}">${d}</div>`;
        }
        html += '</div>';
        wrap.innerHTML = html;
    }

    // Event listeners
    document.getElementById('calPrev').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        renderCalendar();
    });

    document.getElementById('calNext').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        renderCalendar();
    });

    monthSelect.addEventListener('change', () => {
        currentMonth = parseInt(monthSelect.value);
        renderCalendar();
    });

    yearSelect.addEventListener('change', () => {
        currentYear = parseInt(yearSelect.value);
        // Kalau ganti ke tahun ini dan bulan sekarang sudah lewat, reset ke bulan ini
        if (currentYear === now.getFullYear() && currentMonth < now.getMonth()) {
            currentMonth = now.getMonth();
        }
        renderCalendar();
    });

    populateYears();
    renderCalendar();
}

// ============================================
// LIGHTBOX GALERI — detail villa
// ============================================
function initLightbox() {
    const overlay     = document.getElementById('lightboxOverlay');
    if (!overlay) return;

    const lightboxImg = document.getElementById('lightboxImg');
    const thumbsWrap  = document.getElementById('lightboxThumbs');
    const counter     = document.getElementById('lightboxCounter');
    const mainImg     = document.getElementById('galleryMainImg');
    const thumbs      = document.querySelectorAll('.gallery-thumb');

    if (!mainImg) return;

    // Kumpulkan semua foto dari thumbnail
    const photos = Array.from(thumbs).map(t => t.dataset.full);
    // Kalau tidak ada thumbnail (cuma 1 foto), pakai foto utama saja
    if (photos.length === 0 && mainImg) photos.push(mainImg.src);

    let currentIndex = 0;

    // Buat thumbnail di lightbox
    function buildLightboxThumbs() {
        thumbsWrap.innerHTML = photos.map((src, i) => `
            <img src="${src}"
                class="lightbox-thumb ${i === 0 ? 'active' : ''}"
                data-index="${i}"
                alt="Foto ${i + 1}">
        `).join('');

        thumbsWrap.querySelectorAll('.lightbox-thumb').forEach(t => {
            t.addEventListener('click', () => {
                goToLightbox(parseInt(t.dataset.index));
            });
        });
    }

    function goToLightbox(index) {
        currentIndex = Math.max(0, Math.min(index, photos.length - 1));
        lightboxImg.src = photos[currentIndex];
        counter.textContent = `${currentIndex + 1} / ${photos.length}`;

        // Update active thumb
        thumbsWrap.querySelectorAll('.lightbox-thumb').forEach((t, i) => {
            t.classList.toggle('active', i === currentIndex);
        });

        // Scroll thumb ke posisi aktif
        const activeThumb = thumbsWrap.querySelector('.lightbox-thumb.active');
        if (activeThumb) activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

        // Disable prev/next kalau di ujung
        document.getElementById('lightboxPrev').style.opacity = currentIndex === 0 ? '0.3' : '1';
        document.getElementById('lightboxNext').style.opacity = currentIndex === photos.length - 1 ? '0.3' : '1';
    }

    function openLightbox(index) {
        buildLightboxThumbs();
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden'; // prevent scroll background
        goToLightbox(index);
    }

    function closeLightbox() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Klik foto utama → buka lightbox
    mainImg.style.cursor = 'zoom-in';
    mainImg.addEventListener('click', () => {
        // Cari index foto yang sedang aktif di thumbnail
        const activeThumb = document.querySelector('.gallery-thumb.active');
        const idx = activeThumb ? Array.from(thumbs).indexOf(activeThumb) : 0;
        openLightbox(Math.max(0, idx));
    });

    // Klik thumbnail gallery → buka lightbox di foto itu
    thumbs.forEach((thumb, i) => {
        thumb.addEventListener('dblclick', () => openLightbox(i));
    });

    // Navigasi
    document.getElementById('lightboxPrev').addEventListener('click', () => {
        if (currentIndex > 0) goToLightbox(currentIndex - 1);
    });
    document.getElementById('lightboxNext').addEventListener('click', () => {
        if (currentIndex < photos.length - 1) goToLightbox(currentIndex + 1);
    });

    // Tutup
    document.getElementById('lightboxClose').addEventListener('click', closeLightbox);
    overlay.addEventListener('click', e => {
        if (e.target === overlay) closeLightbox();
    });

    // Keyboard navigation
    document.addEventListener('keydown', e => {
        if (!overlay.classList.contains('open')) return;
        if (e.key === 'ArrowLeft')  goToLightbox(currentIndex - 1);
        if (e.key === 'ArrowRight') goToLightbox(currentIndex + 1);
        if (e.key === 'Escape')     closeLightbox();
    });
}

// ============================================
// FORM BOOKING — estimasi harga
// ============================================
function initBookingForm() {
    const form = document.getElementById('bookingForm');
    if (!form) return;

    // ============================================
    // ESTIMASI HARGA — real-time via AJAX
    // ============================================
    const checkInInput  = document.getElementById('inputCheckIn');
    const checkOutInput = document.getElementById('inputCheckOut');
    const villaID       = document.querySelector('[name="villaID"]').value;
    const alertBox = document.querySelector('.date-taken-alert');
    if (alertBox) alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });

    document.querySelector('[data-focus-date]')?.addEventListener('click', () => {
        checkInInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        checkInInput.focus();
        if (checkInInput.showPicker) { try { checkInInput.showPicker(); } catch (e) {} }
    });

    function syncCheckOutFromCheckIn() {
        const todayStr = getTodayLocal();
        const selected = checkInInput.value;

        if (selected < todayStr) {
            checkInInput.value = todayStr; 
        }

        const nextDay = new Date(checkInInput.value + 'T00:00:00');
        nextDay.setDate(nextDay.getDate() + 1);
        const y = nextDay.getFullYear();
        const m = String(nextDay.getMonth() + 1).padStart(2, '0');
        const d = String(nextDay.getDate()).padStart(2, '0');
        const nextDayStr = `${y}-${m}-${d}`;

        checkOutInput.min = nextDayStr;
        checkOutInput.value = nextDayStr; 
    }

    if (!checkInInput.value) {
        checkInInput.value = getTodayLocal();
        syncCheckOutFromCheckIn();
    } else if (!checkOutInput.value) {
        syncCheckOutFromCheckIn();
    }

    // VALIDASI NOMOR WHATSAPP di browser
    const phoneInput = form.querySelector('[name="guest_phone"]');
    const phoneError = document.getElementById('phoneError');
    const validPhone = v => /^(\+62|62|0)8[0-9]{8,12}$/.test(v.replace(/[\s-]/g, '')); // 08xx atau 628xx, total minimal 10 digit

    function showPhoneError(show) {
        phoneError.textContent = show ? 'Nomor WhatsApp tidak valid. Gunakan format 08xxxxxxxxxx dengan minimal 10 digit.' : '';
        phoneError.style.display = show ? 'block' : 'none';
        phoneInput.classList.toggle('input-error', show);
    }

    phoneInput.addEventListener('input', () => { if (validPhone(phoneInput.value)) showPhoneError(false); }); // pesan hilang begitu nomor benar

    form.addEventListener('submit', e => {
        if (validPhone(phoneInput.value)) return;
        e.preventDefault(); // submit dibatalkan, tombol juga tidak masuk loading
        showPhoneError(true);
        phoneError.scrollIntoView({ behavior: 'smooth', block: 'center' }); // pesan di atas kolom tanggal langsung terlihat
    });

    fetchEstimate();

    checkInInput.addEventListener('change', () => { 
        syncCheckOutFromCheckIn();
        fetchEstimate();
    });
    checkOutInput.addEventListener('change', fetchEstimate);

    async function fetchEstimate() {
        const checkIn  = checkInInput.value;
        const checkOut = checkOutInput.value;

        const placeholder = document.getElementById('estimateLoading');
        const content      = document.getElementById('estimateContent');
        const dateError    = document.getElementById('dateError');

        if (!checkIn || !checkOut) {
            placeholder.style.display = 'block';
            content.style.display = 'none';
            return;
        }

        try {
            const params = new URLSearchParams({ villaID, check_in: checkIn, check_out: checkOut });
            const cap = document.getElementById('filter-capacity'); if (cap && cap.value) params.set('capacity', cap.value);
            const res = await fetch('/api/estimate-price?' + params.toString());
            const data = await res.json();

            if (!res.ok) {
                dateError.textContent = data.error || 'Tanggal tidak valid.';
                dateError.style.display = 'block';
                placeholder.style.display = 'block';
                content.style.display = 'none';
                return;
            }

            dateError.style.display = 'none';
            placeholder.style.display = 'none';
            content.style.display = 'block';

            const breakdownEl = document.getElementById('estimateBreakdown');
            breakdownEl.innerHTML = data.breakdown.map(row => `
                <div class="estimate-row">
                    <span class="estimate-row-date">${row.day_name}</span>
                    <span class="estimate-row-price">Rp ${row.price.toLocaleString('id-ID')}</span>
                </div>
            `).join('');

            document.getElementById('estimateTotal').textContent = 'Rp ' + data.total.toLocaleString('id-ID');

        } catch (err) {
            console.error('Gagal memuat estimasi harga', err);
        }
    }
}

// ============================================
// CARD VILLA — halaman /cari, klik card ke detail
// ============================================
function initVillaCardClick() {
    const cards = document.querySelectorAll('.villa-card');
    if (!cards.length) return; // halaman ini tidak punya villa-card, skip

    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', (e) => {
            if (e.target.closest('[data-stop-card-click]')) return; // klik di tombol "Pesan sekarang", jangan redirect ke detail
            window.location.href = card.dataset.href;
        });
    });
}

// ============================================
// TICKER KARTU REKOMENDASI VILLA — halaman /cari
// ============================================
function initTickerCardVilla() {
    const track = document.getElementById('rekomendasiTrack');
    if (!track) return;

    const originalCards = track.children.length;
    if (originalCards === 0) return;

    // Target sekitar 20-24 kartu total di DOM, gak peduli berapa jumlah data asli
    const targetTotal = 24;
    const repeatCount = Math.max(2, Math.ceil(targetTotal / originalCards));

    const original = track.innerHTML;
    track.innerHTML = original.repeat(repeatCount);
}

// ============================================
// HALAMAN NOTA — countdown, polling status, konfirmasi keluar halaman
// ============================================
function initNotaPage() {
    const page = document.getElementById('notaPage');
    if (!page) return; // halaman ini bukan halaman nota, skip

    let expires     = new Date(page.dataset.expires);
    const statusUrl = page.dataset.statusUrl;
    const cancelUrl = page.dataset.cancelUrl;
    const payUrl    = page.dataset.payUrl;
    const qrUrl     = page.dataset.qrUrl; // terisi kalau halaman di-reload setelah QR dibuat
    const csrf      = page.dataset.csrf;
    const cd        = document.getElementById('countdown');
    const modal     = document.getElementById('leaveModal');
    const btnPay    = document.getElementById('btnPay');
    const qrisBox   = document.getElementById('qrisBox');
    const qrisImg   = document.getElementById('qrisImg');
    const holder    = document.getElementById('qrisPlaceholder');

    let leaving   = false; // true kalau keluar halaman sudah disetujui, atau pembayaran selesai
    let target    = null;
    let pollTimer = null;

    // Countdown batas waktu bayar
    function tick() {
        const s = Math.max(0, Math.floor((expires - new Date()) / 1000));
        cd.textContent = `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
        if (s === 0) {
            leaving = true;
            location.reload();
        }
    }
    tick();
    setInterval(tick, 1000);

    // Peringatan bawaan browser: tutup tab, refresh, tombol back
    window.addEventListener('beforeunload', e => {
        if (leaving) return;
        e.preventDefault();
        e.returnValue = '';
    });

    // Klik link atau tombol Kembali di dalam situs: tampilkan popup sendiri
    document.addEventListener('click', e => {
        const el = e.target.closest('a[href], [data-leave]');
        if (!el || el.target === '_blank') return; // preview KTP dibuka di tab baru, jangan dicegat
        const url = el.dataset.leave || el.getAttribute('href');
        if (!url || url.startsWith('#') || url.startsWith('javascript:')) return;
        e.preventDefault();
        target = url;
        modal.hidden = false;
    });

    document.getElementById('leaveStay').addEventListener('click', () => {
        modal.hidden = true;
    });

    document.getElementById('leaveGo').addEventListener('click', async () => {
        leaving = true;
        setButtonLoading(document.getElementById('leaveGo'), true, 'Membatalkan...');
        try {
            await fetch(cancelUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            });
        } catch (err) {
            console.error('Gagal membatalkan booking', err);
        }
        window.location.href = target;
    });

    // Tampilkan QR dan mulai polling status pembayaran
    function showQr(url) {
        qrisImg.src = url;
        qrisImg.hidden = false;
        holder.hidden = true;
        qrisBox.hidden = false;
        btnPay.hidden = true;
        if (!pollTimer) pollTimer = setInterval(pollStatus, 4000);
    }

    // QR hangus: kembali ke tombol Bayar supaya tamu bisa membuat QR baru
    function resetQr() {
        clearInterval(pollTimer);
        pollTimer = null;
        qrisImg.hidden = true;
        qrisBox.hidden = true;
        btnPay.hidden = false;
        btnPay.disabled = false;
    }

    async function pollStatus() {
        try {
            const res  = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
            const data = await res.json();
            if (data.redirect) {
                leaving = true;
                window.location.href = data.redirect;
            } else if (pollTimer && data.qr_active === false) {
                resetQr();
            }
        } catch (err) {
            console.error('Gagal cek status pembayaran', err);
        }
    }

    btnPay.addEventListener('click', async () => {
        setButtonLoading(btnPay, true, 'Membuat QRIS...');
        btnPay.disabled = true;
        qrisBox.hidden = false;
        holder.hidden = false;
        holder.textContent = 'Membuat kode QRIS...';

        try {
            const res  = await fetch(payUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Gagal membuat QRIS.');

            expires = new Date(data.expires_at); // batas waktu diperpanjang sejak QR dibuat
            showQr(data.qr_url);
            setButtonLoading(btnPay, false);
        } catch (err) {
            console.error('Gagal membuat QRIS', err);
            holder.textContent = err.message;
            btnPay.disabled = false;
            setButtonLoading(btnPay, false);
        }
    });

    if (qrUrl) showQr(qrUrl); // halaman di-reload setelah QR dibuat
}

// ============================================
// LOADING TOMBOL — dipakai semua halaman
// ============================================
const LOADING_TEXT = {
    'btn-submit-booking': 'Memproses booking...',
};

const LOADING_ICON_ONLY = ['btn-cek-booking', 'search-btn'];

function loadingLabelFor(btn) {
    if (!btn) return 'Memproses...';
    if (btn.dataset.loadingText) return btn.dataset.loadingText;
    const key = Object.keys(LOADING_TEXT).find(c => btn.classList.contains(c));
    return key ? LOADING_TEXT[key] : 'Memproses...';
}

function setButtonLoading(btn, on, text) {
    if (!btn) return;

    if (on) {
        if (btn.classList.contains('is-loading')) return; // sudah loading, abaikan
        const iconOnly = btn.hasAttribute('data-loading-icon') || LOADING_ICON_ONLY.some(c => btn.classList.contains(c)); // data-loading-icon untuk tombol lain di blade
        btn.dataset.originalHtml = btn.innerHTML; // simpan isi asli untuk dipulihkan

        if (iconOnly) {
            btn.style.width  = btn.offsetWidth + 'px'; // lebar dikunci persis, tombol tidak berubah ukuran
            btn.style.height = btn.offsetHeight + 'px'; // tinggi juga dikunci
            btn.setAttribute('aria-label', 'Memuat'); // pembaca layar tetap tahu tombol sedang bekerja
        } else {
            btn.style.minWidth = btn.offsetWidth + 'px'; // tombol bertulisan: lebar minimum dikunci
        }

        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');
        if (!btn.form) btn.disabled = true; // tombol non-form dinonaktifkan penuh, tombol submit dijaga lewat flag form

        const inner   = document.createElement('span');
        const spinner = document.createElement('span');
        inner.className   = 'btn-loading-inner';
        spinner.className = 'btn-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        inner.append(spinner);

        if (!iconOnly) { // teks status hanya untuk tombol yang bukan icon-only
            const label = document.createElement('span');
            label.textContent = text || loadingLabelFor(btn);
            inner.append(label);
        }

        btn.replaceChildren(inner);
    } else {
        if (!btn.classList.contains('is-loading')) return;
        btn.innerHTML = btn.dataset.originalHtml ?? btn.innerHTML;
        delete btn.dataset.originalHtml;
        btn.style.width = '';
        btn.style.height = '';
        btn.style.minWidth = '';
        btn.removeAttribute('aria-label');
        btn.classList.remove('is-loading');
        btn.removeAttribute('aria-busy');
        if (!btn.form) btn.disabled = false;
    }
}

function initLoadingButtons() {
    document.addEventListener('submit', e => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || form.hasAttribute('data-no-loading')) return;
        if (e.defaultPrevented) return;

        if (form.dataset.submitting === '1') {
            e.preventDefault();
            return;
        }
        form.dataset.submitting = '1';

        const btn = e.submitter || form.querySelector('[type="submit"]');
        setButtonLoading(btn, true);
    });

    window.addEventListener('pageshow', e => {
        if (!e.persisted) return;
        document.querySelectorAll('.is-loading').forEach(b => setButtonLoading(b, false));
        document.querySelectorAll('form[data-submitting]').forEach(f => delete f.dataset.submitting);
    });
}