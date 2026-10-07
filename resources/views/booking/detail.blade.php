<div class="nota-detail">
    <div class="nota-row"><span>Villa</span><strong>{{ $booking->villa->name }}</strong></div>
    <div class="nota-row"><span>Alamat</span><strong>{{ $booking->villa->address }}</strong></div>
    <div class="nota-row"><span>Kapasitas</span><strong>{{ $booking->villa->capacity }} orang</strong></div>
    <div class="nota-row"><span>Check-in</span><strong>{{ $booking->check_in->translatedFormat('l, d F Y') }}</strong></div>
    <div class="nota-row"><span>Check-out</span><strong>{{ $booking->check_out->translatedFormat('l, d F Y') }}</strong></div>
    <div class="nota-row"><span>Total Malam</span><strong>{{ $nights }} malam</strong></div>
    <div class="nota-row"><span>Nama Tamu</span><strong>{{ $booking->guest_name }}</strong></div>
    <div class="nota-row"><span>No. Telepon</span><strong>{{ $booking->guest_phone }}</strong></div>
    @if (!empty($showKtp))
        <div class="nota-row">
            <span>Scan KTP</span>
            <a href="{{ $ktpUrl }}" target="_blank"><img src="{{ $ktpUrl }}" class="nota-ktp" alt="Scan KTP"></a>
        </div>
    @endif
</div>