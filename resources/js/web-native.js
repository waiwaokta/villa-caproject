document.addEventListener('DOMContentLoaded', () => {
    initSliderWisma();
    initSearchFilter();
    initNavHamburger();
    initGallery();
    initAvailabilityCalendar();
    initBookingForm();
});

    function getTodayLocal() {
        const now = new Date();
        const year  = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day   = String(now.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
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
    if (!searchBtn) return; // halaman ini tidak punya search box, skip

    window.doSearch = function () {
        const lokasi   = document.getElementById('filter-lokasi').value;
        const checkin  = document.getElementById('filter-checkin').value;
        const checkout = document.getElementById('filter-checkout').value;
        const usertype = document.getElementById('filter-usertype').value;
        const params   = new URLSearchParams({
            lokasi,
            check_in: checkin,
            check_out: checkout,
            user_type: usertype
        });
        window.location.href = '/?' + params.toString();
    };

    const checkinInput = document.getElementById('filter-checkin');
    if (checkinInput) {
        checkinInput.addEventListener('change', function () {
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            document.getElementById('filter-checkout').min = nextDay.toISOString().split('T')[0];
        });
    }
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
// KALENDER AVAILABILITY — detail wisma, 3 bulan ke depan
// ============================================
function initAvailabilityCalendar() {
    const wrap = document.getElementById('calendarWrap');
    if (!wrap) return;

    const occupiedDates = JSON.parse(wrap.dataset.occupied || '[]');
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    let html = '';

    for (let m = 0; m < 3; m++) {
        const monthDate = new Date(today.getFullYear(), today.getMonth() + m, 1);
        const year = monthDate.getFullYear();
        const month = monthDate.getMonth();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDayOfWeek = new Date(year, month, 1).getDay();

        html += `<div class="cal-month">`;
        html += `<div class="cal-month-title">${monthNames[month]} ${year}</div>`;
        html += `<div class="cal-grid">`;

        dayLabels.forEach(label => {
            html += `<div class="cal-day-label">${label}</div>`;
        });

        // Kosong sebelum tanggal 1
        for (let i = 0; i < firstDayOfWeek; i++) {
            html += `<div class="cal-day cal-day-empty"></div>`;
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dateObj = new Date(year, month, d);
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

            let cls = 'cal-day-available';
            if (dateObj < today) {
                cls = 'cal-day-past';
            } else if (occupiedDates.includes(dateStr)) {
                cls = 'cal-day-occupied';
            }

            html += `<div class="cal-day ${cls}">${d}</div>`;
        }

        html += `</div></div>`;
    }

    wrap.innerHTML = html;
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

        // booking_type cuma relevan kalau umum
        bookingTypeWrap.style.display = isPln ? 'none' : 'grid';

        // employee_id cuma kalau PLN
        employeeIdWrap.style.display = isPln ? 'block' : 'none';

        // instansi fields cuma kalau umum-instansi
        instansiWrap.style.display = isInstansi ? 'block' : 'none';

        // Dokumen
        docKtpWrap.style.display        = isPln ? 'none' : 'block';
        docNpwpWrap.style.display       = isInstansi ? 'block' : 'none';
        docIdPlnWrap.style.display      = isPln ? 'block' : 'none';
        docPlnKtpNpwpWrap.style.display = isPln ? 'block' : 'none';

        // Toggle required attribute biar validasi browser konsisten sama backend
        document.querySelector('[name="doc_ktp"]').required = !isPln;
        document.querySelector('[name="doc_npwp"]').required = isInstansi;
        document.querySelector('[name="doc_id_pln"]').required = isPln;
    }

    userTypeRadios.forEach(r => r.addEventListener('change', () => {
        updateFieldVisibility();
        fetchEstimate();
    }));
    bookingTypeRadios.forEach(r => r.addEventListener('change', updateFieldVisibility));

    updateFieldVisibility(); // jalankan sekali di awal

    // ============================================
    // ESTIMASI HARGA — real-time via AJAX
    // ============================================
    const checkInInput  = document.getElementById('inputCheckIn');
    const checkOutInput = document.getElementById('inputCheckOut');
    const wismaID        = document.querySelector('[name="wismaID"]').value;

    checkInInput.addEventListener('change', () => {
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
        checkOutInput.min = `${y}-${m}-${d}`;
        checkOutInput.value = '';
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
}