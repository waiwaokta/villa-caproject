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
        @if ($errors->any())
    <div class="form-error" style="display:block; margin-bottom:20px;">
        <strong>Ditemukan kesalahan:</strong>
        <ul style="margin-top:8px; padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        @csrf
        <input type="hidden" name="villaID" value="{{ $villa->villaID }}">

        <div class="booking-grid">
            <div class="booking-main">

                {{-- STEP 1: TANGGAL --}}
                <div class="form-section">
                    <h2><span class="step-badge">1</span> Pilih Tanggal</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Check-in</label>
                            <input type="date" name="check_in" id="inputCheckIn" required min="{{ date('Y-m-d') }}" value="{{ $prefillCheckIn }}">
                        </div>
                        <div class="form-group">
                            <label>Check-out</label>
                            <input type="date" name="check_out" id="inputCheckOut" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $prefillCheckOut }}"> {{-- DIUBAH: tambah atribut min --}}
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
                        <input type="text" name="guest_name" required maxlength="255" placeholder="Nama sesuai KTP">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>No. WhatsApp Aktif</label>
                            <input type="text" name="guest_phone" required maxlength="15" placeholder="08123456789">
                        </div>
                        <div class="form-group" id="docKtpWrap"> {{-- DIUBAH: dipindah ke sebelah WhatsApp, Step 3 dihapus --}}
                            <label>Foto/Scan KTP</label>
                            <input type="file" name="doc_ktp" accept=".jpg,.jpeg,.png" required> {{-- DIUBAH: tambah required, hanya gambar --}}
                            <p class="form-hint">Format JPG atau PNG. Maks 2MB.</p> {{-- DIUBAH: PDF dihapus --}}
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