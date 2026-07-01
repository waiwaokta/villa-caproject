@extends('layouts.app')

@section('title', 'Booking - ' . $wisma->name . ' - Wisma PLN')

@section('content')

<div class="booking-page">

    <div class="booking-header">
        <a href="/wisma/{{ $wisma->wismaID }}" class="back-link">
            <i class="ti ti-arrow-left"></i> Kembali ke detail wisma
        </a>
        <h1>Booking {{ $wisma->name }}</h1>
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
        <input type="hidden" name="wismaID" value="{{ $wisma->wismaID }}">

        <div class="booking-grid">
            <div class="booking-main">

                {{-- STEP 1: TANGGAL --}}
                <div class="form-section">
                    <h2><span class="step-badge">1</span> Pilih Tanggal</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Check-in</label>
                            <input type="date" name="check_in" id="inputCheckIn" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Check-out</label>
                            <input type="date" name="check_out" id="inputCheckOut" required>
                        </div>
                    </div>
                    <p class="form-hint"><i class="ti ti-info-circle"></i> Check-in mulai 14:00, check-out maksimal 12:00</p>
                    <div id="dateError" class="form-error" style="display:none;"></div>
                </div>

                {{-- STEP 2: STATUS PENGGUNA --}}
                <div class="form-section">
                    <h2><span class="step-badge">2</span> Status Pemesan</h2>
                    <div class="radio-group">
                        <label class="radio-card">
                            <input type="radio" name="user_type" value="umum" checked>
                            <div class="radio-card-content">
                                <i class="ti ti-user"></i>
                                <span>Umum</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="user_type" value="pln">
                            <div class="radio-card-content">
                                <i class="ti ti-id-badge-2"></i>
                                <span>Pegawai / Pensiunan PLN</span>
                            </div>
                        </label>
                    </div>

                    {{-- Hanya muncul kalau user_type = umum --}}
                    <div id="bookingTypeWrap" class="radio-group" style="margin-top:14px;">
                        <label class="radio-card">
                            <input type="radio" name="booking_type" value="perorangan" checked>
                            <div class="radio-card-content">
                                <i class="ti ti-user-circle"></i>
                                <span>Perorangan</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="booking_type" value="instansi">
                            <div class="radio-card-content">
                                <i class="ti ti-building"></i>
                                <span>Instansi</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- STEP 3: DATA TAMU --}}
                <div class="form-section">
                    <h2><span class="step-badge">3</span> Data Pemesan</h2>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="guest_name" required maxlength="255" placeholder="Nama sesuai KTP">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>No. WhatsApp Aktif</label>
                            <input type="text" name="guest_phone" required maxlength="15" placeholder="08123456789">
                        </div>
                        <div class="form-group">
                            <label>NIK KTP</label>
                            <input type="text" name="guest_ktp" required maxlength="16" placeholder="16 digit NIK">
                        </div>
                    </div>

                    {{-- Hanya muncul kalau user_type = pln --}}
                    <div id="employeeIdWrap" class="form-group" style="display:none;">
                        <label>ID Pegawai / Pensiunan PLN</label>
                        <input type="text" name="employee_id" maxlength="20" placeholder="Nomor ID pegawai">
                    </div>

                    {{-- Hanya muncul kalau booking_type = instansi --}}
                    <div id="instansiWrap" style="display:none;">
                        <div class="form-group">
                            <label>Nama Instansi</label>
                            <input type="text" name="inst_name" maxlength="255" placeholder="Nama perusahaan/instansi">
                        </div>
                        <div class="form-group">
                            <label>NPWP Instansi</label>
                            <input type="text" name="inst_npwp" maxlength="20" placeholder="Nomor NPWP instansi">
                        </div>
                    </div>
                </div>

                {{-- STEP 4: DOKUMEN --}}
                <div class="form-section">
                    <h2><span class="step-badge">4</span> Unggah Dokumen</h2>

                    {{-- Umum: KTP wajib --}}
                    <div id="docKtpWrap" class="form-group">
                        <label>Foto/Scan KTP</label>
                        <input type="file" name="doc_ktp" accept=".jpg,.jpeg,.png,.pdf">
                        <p class="form-hint">Format JPG, PNG, atau PDF. Maks 2MB.</p>
                    </div>

                    {{-- Umum-Instansi: NPWP wajib --}}
                    <div id="docNpwpWrap" class="form-group" style="display:none;">
                        <label>Foto/Scan NPWP Instansi</label>
                        <input type="file" name="doc_npwp" accept=".jpg,.jpeg,.png,.pdf">
                        <p class="form-hint">Format JPG, PNG, atau PDF. Maks 2MB.</p>
                    </div>

                    {{-- PLN: ID Card wajib --}}
                    <div id="docIdPlnWrap" class="form-group" style="display:none;">
                        <label>Foto/Scan ID Card Pegawai/Pensiunan PLN</label>
                        <input type="file" name="doc_id_pln" accept=".jpg,.jpeg,.png,.pdf">
                        <p class="form-hint">Format JPG, PNG, atau PDF. Maks 2MB.</p>
                    </div>

                    {{-- PLN: KTP/NPWP, pilih salah satu — CUMA 1 FIELD --}}
                    <div id="docPlnKtpNpwpWrap" class="form-group" style="display:none;">
                        <label>Foto/Scan KTP atau NPWP <span class="optional-tag">(pilih salah satu)</span></label>
                        <input type="file" name="doc_ktp_pln" accept=".jpg,.jpeg,.png,.pdf">
                        <p class="form-hint"><i class="ti ti-info-circle"></i> Lampirkan KTP atau NPWP, salah satu saja</p>
                    </div>

                    {{-- Semua: bukti bayar wajib --}}
                    <div class="form-group">
                        <label>Bukti Pelunasan Pembayaran</label>
                        <input type="file" name="doc_bukti_bayar" required accept=".jpg,.jpeg,.png,.pdf">
                        <p class="form-hint">Transfer ke rekening yang tertera, lalu unggah bukti transfer.</p>
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