document.addEventListener('DOMContentLoaded', () => {
    initSliderWisma();
    initSearchFilter();
    initNavHamburger();
    initGallery();
    initLightbox();
    initAvailabilityCalendar();
    initBookingForm();
    initStatTicker();
    initWismaCardClick();
    initWismaSearchDateGuard();
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
    // SLIDER WISMA — beranda
    // ============================================
    function initSliderWisma() {
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

    function syncCheckoutMin() { // ditambahkan — function terpisah biar bisa dipanggil langsung di awal juga
        if (!checkinInput.value) return;
        const nextDay = new Date(checkinInput.value + 'T00:00:00');
        nextDay.setDate(nextDay.getDate() + 1);
        const y = nextDay.getFullYear();
        const m = String(nextDay.getMonth() + 1).padStart(2, '0');
        const d = String(nextDay.getDate()).padStart(2, '0');
        const minCheckout = `${y}-${m}-${d}`;
        checkoutInput.min = minCheckout;
        if (checkoutInput.value && checkoutInput.value < minCheckout) { // ditambahkan — kalau value sekarang udah invalid, reset otomatis
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
// VALIDASI TANGGAL — form cari wisma (/cari)
// ============================================
function initWismaSearchDateGuard() {
    const form = document.querySelector('.wisma-box-standalone');
    if (!form) return; // halaman ini tidak punya form cari wisma, skip

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
// NAV HAMBURGER — mobile menu, semua halaman
// ============================================
function initNavHamburger() {
    const hamburger = document.querySelector('.nav-hamburger');
    const mobileMenu = document.querySelector('.nav-mobile-menu');
    if (!hamburger || !mobileMenu) return;

    hamburger.addEventListener('click', () => {
        mobileMenu.classList.toggle('open');
    });
}

// ============================================
// INIT — jalankan semua function di atas setelah DOM siap
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    initSliderWisma();
    initSearchFilter();
    initNavHamburger();
});

// ============================================
// GALERI FOTO — detail wisma
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
// KALENDER AVAILABILITY — detail wisma
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
            if (isToday)         cls += 'cal-day-today';
            else if (isPast)     cls += 'cal-day-past';
            else if (isOccupied) cls += 'cal-day-occupied';
            else                 cls += 'cal-day-available';

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
// LIGHTBOX GALERI — detail wisma
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
// FORM BOOKING — show/hide field dinamis + estimasi harga
// ============================================
function initBookingForm() {
    const form = document.getElementById('bookingForm');
    if (!form) return;

    const userTypeRadios    = document.querySelectorAll('input[name="user_type"]');
    const bookingTypeRadios = document.querySelectorAll('input[name="booking_type"]');
    const bookingTypeWrap   = document.getElementById('bookingTypeWrap');
    const employeeIdWrap    = document.getElementById('employeeIdWrap');
    const instansiWrap      = document.getElementById('instansiWrap');
    const docKtpWrap        = document.getElementById('docKtpWrap');
    const docNpwpWrap       = document.getElementById('docNpwpWrap');
    const docIdPlnWrap      = document.getElementById('docIdPlnWrap');
    const docPlnKtpNpwpWrap = document.getElementById('docPlnKtpNpwpWrap');

    function getUserType() {
        return document.querySelector('input[name="user_type"]:checked')?.value;
    }
    function getBookingType() {
        return document.querySelector('input[name="booking_type"]:checked')?.value;
    }

    function updateFieldVisibility() {
        const userType    = getUserType();
        const bookingType = getBookingType();
        const isPln       = userType === 'pln';
        const isInstansi  = !isPln && bookingType === 'instansi';

        bookingTypeWrap.style.display = isPln ? 'none' : 'grid';
        employeeIdWrap.style.display = isPln ? 'block' : 'none';
        instansiWrap.style.display = isInstansi ? 'block' : 'none';

        docKtpWrap.style.display        = isPln ? 'none' : 'block';
        docNpwpWrap.style.display       = isInstansi ? 'block' : 'none';
        docIdPlnWrap.style.display      = isPln ? 'block' : 'none';
        docPlnKtpNpwpWrap.style.display = isPln ? 'block' : 'none';

        document.querySelector('[name="doc_ktp"]').required = !isPln;
        document.querySelector('[name="doc_npwp"]').required = isInstansi;
        document.querySelector('[name="doc_id_pln"]').required = isPln;
    }

    userTypeRadios.forEach(r => r.addEventListener('change', () => {
        updateFieldVisibility();
        fetchEstimate();
    }));
    bookingTypeRadios.forEach(r => r.addEventListener('change', updateFieldVisibility));

    updateFieldVisibility();

    // ============================================
    // ESTIMASI HARGA — real-time via AJAX
    // ============================================
    const checkInInput  = document.getElementById('inputCheckIn');
    const checkOutInput = document.getElementById('inputCheckOut');
    const wismaID       = document.querySelector('[name="wismaID"]').value;

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

    fetchEstimate();

    checkInInput.addEventListener('change', () => { 
        syncCheckOutFromCheckIn();
        fetchEstimate();
    });
    checkOutInput.addEventListener('change', fetchEstimate);

    async function fetchEstimate() {
        const checkIn  = checkInInput.value;
        const checkOut = checkOutInput.value;
        const userType = getUserType();

        const placeholder = document.getElementById('estimateLoading');
        const content      = document.getElementById('estimateContent');
        const dateError    = document.getElementById('dateError');

        if (!checkIn || !checkOut) {
            placeholder.style.display = 'block';
            content.style.display = 'none';
            return;
        }

        try {
            const params = new URLSearchParams({ wismaID, user_type: userType, check_in: checkIn, check_out: checkOut });
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

    // // Kalau tanggal sudah terisi otomatis dari prefill (misal dari halaman /cari), langsung fetch estimasi tanpa perlu user pilih ulang
    // checkInInput.addEventListener('change', () => {
    //     const todayStr = getTodayLocal();
    //     const selected = checkInInput.value;

    //     if (selected < todayStr) {
    //         checkInInput.value = todayStr;
    //     }

    //     const nextDay = new Date(checkInInput.value + 'T00:00:00');
    //     nextDay.setDate(nextDay.getDate() + 1);
    //     const y = nextDay.getFullYear();
    //     const m = String(nextDay.getMonth() + 1).padStart(2, '0');
    //     const d = String(nextDay.getDate()).padStart(2, '0');
    //     const nextDayStr = `${y}-${m}-${d}`;

    //     checkOutInput.min = nextDayStr;
    //     checkOutInput.value = nextDayStr;
    //     fetchEstimate();
    // });
}

// ============================================
// CARD WISMA — halaman /cari, klik card ke detail
// ============================================
function initWismaCardClick() {
    const cards = document.querySelectorAll('.wisma-card');
    if (!cards.length) return; // halaman ini tidak punya wisma-card, skip

    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', (e) => {
            if (e.target.closest('[data-stop-card-click]')) return; // klik di tombol "Pesan sekarang", jangan redirect ke detail
            window.location.href = card.dataset.href;
        });
    });
}