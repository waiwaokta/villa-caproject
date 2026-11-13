@extends('layouts.app')

@section('title', 'Booking - ' . $villa->name . ' - NginapYuk')

@section('content')

<div class="booking-page">

    <div class="booking-header">
        <a href="/villa/{{ $villa->villaID }}" class="back-link">
            <i class="ti ti-arrow-left"></i> Kembali ke detail villa
        </a>
        <h1>Booking {{ $villa->name }}</h1>
        <p>Lengkapi data di bawah untuk melanjutkan booking</p>
    </div>

    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" class="booking-form">
                @if ($errors->has('date_taken'))
            <div class="date-taken-alert" role="alert">
                <div class="date-taken-icon"><i class="ti ti-calendar-x"></i></div>
                <div class="date-taken-body">
                    <h3>Tanggal sudah dibooking tamu lain</h3>
                    <p>Tanggal yang Anda pilih baru saja diambil tamu lain, jadi booking Anda belum dibuat dan belum ada pembayaran yang diproses. Silakan pilih tanggal lain atau cek kalender ketersediaan villa.</p>
                    <p class="date-taken-note">Data diri Anda sudah terisi kembali, tetapi foto KTP perlu dipilih ulang.</p>
                    <div class="date-taken-actions">
                        <button type="button" class="btn-back-home" data-focus-date><i class="ti ti-calendar-event"></i> Pilih Tanggal Lain</button>
                        <a href="/villa/{{ $villa->villaID }}#ketersediaan" class="btn-cek-status"><i class="ti ti-calendar-search"></i> Lihat Ketersediaan</a>
                    </div>
                </div>
            </div>
        @endif

        @php $otherErrors = collect($errors->getMessages())->except('date_taken')->flatten(); @endphp {{-- error lain tetap tampil sebagai daftar --}}
        @csrf
        <input type="hidden" name="villaID" value="{{ $villa->villaID }}">

        <div class="booking-grid">
            <div class="booking-main">

                {{-- STEP 1: TANGGAL --}}
                <div class="form-section">
                    <h2><span class="step-badge">1</span> Pilih Tanggal</h2>
                    <div class="form-errors-slot"> {{-- semua pesan error form dikumpulkan di sini, di atas kolom tanggal --}}
                        @if ($otherErrors->isNotEmpty())
                            <div class="form-error" role="alert">
                                <strong>Periksa kembali data Anda:</strong>
                                <ul>
                                    @foreach ($otherErrors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div id="dateError" class="form-error" style="display:none;"></div>
                        <div id="phoneError" class="form-error" style="display:none;"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Check-in</label>
                            <input type="date" name="check_in" id="inputCheckIn" required min="{{ date('Y-m-d') }}" value="{{ old('check_in', $prefillCheckIn) }}" class="{{ $errors->has('date_taken') ? 'input-error' : '' }}">
                        </div>
                        <div class="form-group">
                            <label>Check-out</label>
                            <input type="date" name="check_out" id="inputCheckOut" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $prefillCheckOut }}" class="{{ $errors->has('date_taken') ? 'input-error' : '' }}">
                        </div>
                    </div>
                    <p class="form-hint"><i class="ti ti-info-circle"></i> Check-in mulai 14:00, check-out maksimal 12:00</p>
                    <div id="dateError" class="form-error" style="display:none;"></div>
                </div>

                {{-- STEP 2: DATA TAMU --}}
                <div class="form-section">
                    <h2><span class="step-badge">2</span> Data Pemesan</h2>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="guest_name" required maxlength="255" placeholder="Nama sesuai KTP" value="{{ old('guest_name') }}">                    
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>No. WhatsApp Aktif</label>
                            <input type="text" name="guest_phone" required maxlength="15" inputmode="tel" placeholder="08123456789" value="{{ old('guest_phone') }}" class="{{ $errors->has('guest_phone') ? 'input-error' : '' }}"> {{-- inputmode memunculkan keypad angka di HP --}}                        </div>
                        <div class="form-group" id="docKtpWrap">
                            <label>Foto/Scan KTP</label>
                            <input type="file" name="doc_ktp" accept=".jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR ESTIMASI HARGA --}}
            <div class="booking-sidebar">
                <div class="estimate-card">
                    <h3>Estimasi Harga</h3>
                    <div id="estimateLoading" class="estimate-placeholder">
                        <i class="ti ti-calendar-event"></i>
                        <p>Pilih tanggal untuk melihat estimasi harga</p>
                    </div>
                    <div id="estimateContent" style="display:none;">
                        <div id="estimateBreakdown" class="estimate-breakdown"></div>
                        <div class="estimate-total">
                            <span>Total</span>
                            <strong id="estimateTotal">Rp 0</strong>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit-booking" id="submitBtn">
                        <i class="ti ti-send"></i> Kirim Booking
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection