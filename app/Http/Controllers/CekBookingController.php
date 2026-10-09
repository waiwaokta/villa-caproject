<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class CekBookingController extends Controller
{
    public function index()
    {
        return view('booking.check-book');
    }

    public function cari(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string', 'max:50'],
            'guest_phone'  => ['required', 'string', 'max:20'], // ditambahkan
        ], [
            'booking_code.required' => 'Kode booking wajib diisi.',
            'guest_phone.required'  => 'Nomor WhatsApp wajib diisi.',
        ]);

        // Bersihkan input, user mungkin copy-paste dengan spasi tersisa
        $bookingID = trim($request->booking_code);

        // HANYA select kolom yang AMAN ditampilkan ke publik
        $booking = Booking::with(['villa:villaID,name,location'])
            ->select([
                'bookingID',
                'villaID',
                'check_in',
                'check_out',
                'total_nights',
                'total_price',
                'guest_name',
                'guest_phone', // ditambahkan, hanya untuk verifikasi dan tidak ditampilkan
                'status',
                'reject_desc',
                'expires_at',  // ditambahkan
                'paid_at',     // ditambahkan
                'created_at',
                'updated_at',
            ])
            ->where('bookingID', $bookingID)
            ->first();

        // Pesan sengaja sama untuk kode salah dan nomor tidak cocok, supaya kode tidak bisa ditebak satu per satu
        if (!$booking || !hash_equals($this->normalizePhone($booking->guest_phone), $this->normalizePhone($request->guest_phone))) {
            throw ValidationException::withMessages([
                'booking_code' => 'Kode booking atau nomor WhatsApp tidak cocok. Periksa kembali data yang Anda masukkan.',
            ]);
        }

        $booking->makeHidden('guest_phone');

        // Status tampilan: pending yang sudah lewat batas waktu dianggap kedaluwarsa
        $state = $booking->status;
        if ($state === 'pending' && $booking->expires_at?->isPast()) {
            $state = 'expired';
        }

        return view('booking.check-book', [
            'booking'    => $booking,
            'state'      => $state,
            'receiptUrl' => $state === 'approved' ? $this->signedUrl('booking.receipt', $bookingID) : null,
            'payUrl'     => $state === 'pending' ? $this->signedUrl('booking.nota', $bookingID) : null,
        ]);
    }

    private function signedUrl(string $route, string $bookingID, int $minutes = 30): string
    {
        return URL::temporarySignedRoute($route, now()->addMinutes($minutes), ['bookingID' => $bookingID]);
    }

    // Samakan format nomor: hanya angka, awalan 62 diubah jadi 0
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }
}