<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
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
            'booking_code' => ['required', 'string'],
        ], [
            'booking_code.required' => 'Kode booking wajib diisi.',
        ]);

        // Bersihkan input — user mungkin copy-paste dengan spasi tersisa
        $bookingID = trim($request->booking_code);

        // ⚠️ HANYA select kolom yang AMAN ditampilkan ke publik — TIDAK ambil guest_phone, guest_ktp, employee_id, inst_npwp, atau relasi dokumen
        $booking = Booking::with(['wisma:wismaID,name,location'])
            ->select([
                'bookingID',
                'villaID',
                'check_in',
                'check_out',
                'total_nights',
                'total_price',
                'guest_name',
                'status',
                'reject_desc',
                'created_at',
                'updated_at',
            ])
            ->where('bookingID', $bookingID)
            ->first();

        if (!$booking) {
            throw ValidationException::withMessages([
                'booking_code' => 'Kode booking tidak ditemukan. Periksa kembali kode yang Anda masukkan.',
            ]);
        }

        return view('booking.check-book', compact('booking'));
    }
}