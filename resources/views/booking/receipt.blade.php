@php
    $tgl   = fn ($d) => $d->copy()->locale('id')->translatedFormat('l, d F Y');
    $waktu = fn ($d) => $d ? $d->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') . ' WIB' : '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota {{ $booking->bookingID }}</title>
    <style>
        @page { margin: 30px 34px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }
        .head td { padding: 0; vertical-align: middle; }
        .brand { font-size: 17px; font-weight: bold; color: #0f766e; }
        .doc-title { text-align: right; font-size: 12px; font-weight: bold; letter-spacing: 1px; }
        .line { border-top: 2px solid #0f766e; margin: 10px 0 14px; }
        .code-box { border: 1px solid #99f6e4; background: #f0fdfa; padding: 10px; text-align: center; margin-bottom: 14px; }
        .code-label { font-size: 9px; color: #6b7280; letter-spacing: 1px; }
        .code-value { font-size: 17px; font-weight: bold; letter-spacing: 1px; margin-top: 3px; }
        .section { font-size: 9px; font-weight: bold; color: #0f766e; letter-spacing: 1px; margin: 14px 0 2px; }
        .rows td { padding: 6px 0; border-bottom: 1px dashed #d1d5db; vertical-align: top; }
        .rows td.label { width: 36%; color: #6b7280; }
        .rows td.value { text-align: right; font-weight: bold; }
        .total td { padding: 14px 0 0; vertical-align: middle; }
        .total .label { font-size: 11px; color: #6b7280; }
        .total .amount { text-align: right; font-size: 16px; font-weight: bold; color: #0f766e; }
        .badge { background: #dcfce7; color: #166534; padding: 3px 10px; font-weight: bold; font-size: 10px; }
        .footer { margin-top: 24px; text-align: center; font-size: 9px; color: #6b7280; line-height: 1.5; }
    </style>
</head>
<body>
    <table class="head">
        <tr>
            <td class="brand">NginapYuk</td>
            <td class="doc-title">NOTA PEMBAYARAN</td>
        </tr>
    </table>
    <div class="line"></div>

    <div class="code-box">
        <div class="code-label">KODE BOOKING</div>
        <div class="code-value">{{ $booking->bookingID }}</div>
    </div>

    <div class="section">DETAIL VILLA</div>
    <table class="rows">
        <tr><td class="label">Villa</td><td class="value">{{ $booking->villa->name }}</td></tr>
        <tr><td class="label">Alamat</td><td class="value">{{ $booking->villa->address }}</td></tr>
        <tr><td class="label">Kapasitas</td><td class="value">{{ $booking->villa->capacity }} orang</td></tr>
    </table>

    <div class="section">DETAIL MENGINAP</div>
    <table class="rows">
        <tr><td class="label">Check-in</td><td class="value">{{ $tgl($booking->check_in) }}</td></tr>
        <tr><td class="label">Check-out</td><td class="value">{{ $tgl($booking->check_out) }}</td></tr>
        <tr><td class="label">Total Malam</td><td class="value">{{ $booking->total_nights }} malam</td></tr>
        <tr><td class="label">Jam</td><td class="value">Check-In 14:00, Check-Out 12:00</td></tr>
    </table>

    <div class="section">DATA TAMU</div>
    <table class="rows">
        <tr><td class="label">Nama Tamu</td><td class="value">{{ $booking->guest_name }}</td></tr>
        <tr><td class="label">No. Telepon</td><td class="value">{{ $booking->guest_phone }}</td></tr>
    </table>

    <div class="section">PEMBAYARAN</div>
    <table class="rows">
        <tr><td class="label">Metode</td><td class="value">QRIS</td></tr>
        <tr><td class="label">Waktu Bayar</td><td class="value">{{ $waktu($booking->paid_at) }}</td></tr>
        <tr><td class="label">Ref. Pembayaran</td><td class="value">{{ $booking->payment_order_id ?? '-' }}</td></tr>
    </table>

    <table class="total">
        <tr>
            <td class="label">Total Dibayar &nbsp; <span class="badge">LUNAS</span></td>
            <td class="amount">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Nota ini adalah bukti pemesanan dan pembayaran yang sah.<br>
        Untuk pembatalan atau pengembalian dana, hubungi CS NginapYuk dengan menyebutkan kode booking.<br>
        Dicetak pada {{ $waktu(now()) }}
    </div>
</body>
</html>