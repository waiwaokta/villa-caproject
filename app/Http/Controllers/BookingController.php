<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Document;
use App\Models\Price;
use App\Models\Wisma;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request)
    {
        $wisma = Wisma::where('wismaID', $request->wismaID)
            ->where('is_active', true)
            ->firstOrFail();

        $checkIn  = \Carbon\Carbon::parse($request->check_in);
        $checkOut = \Carbon\Carbon::parse($request->check_out);
        $nights   = $checkIn->diffInDays($checkOut);

        if ($nights < 1) {
            return back()->withErrors(['check_out' => 'Minimal menginap 1 malam.']);
        }

        // Hitung total price dari tabel prices (snapshot saat booking)
        $dayType     = $this->resolveDayType($checkIn);
        $priceRecord = Price::where('wismaID', $request->wismaID)
            ->where('user_type', $request->user_type)
            ->where('day_type', $dayType)
            ->firstOrFail();

        $totalPrice = $priceRecord->price * $nights;

        // Semua operasi dalam satu transaksi — gagal satu, semua rollback
        DB::transaction(function () use ($request, $wisma, $nights, $totalPrice) {

            $booking = Booking::create([
                'user_id'      => Auth::id(), // null kalau guest
                'wismaID'      => $wisma->wismaID,
                'check_in'     => $request->check_in,
                'check_out'    => $request->check_out,
                'total_nights' => $nights,
                'total_price'  => $totalPrice,
                'user_type'    => $request->user_type,
                'booking_type' => $request->booking_type,
                'guest_name'   => $request->guest_name,
                'guest_phone'  => $request->guest_phone,
                'guest_ktp'    => $request->guest_ktp,
                'employee_id'  => $request->employee_id,
                'inst_name'    => $request->inst_name,
                'inst_npwp'    => $request->inst_npwp,
                'status'       => 'pending',
            ]);

            // Upload semua dokumen
            $this->uploadDocument($booking->bookingID, 'ktp', $request->file('doc_ktp'));
            $this->uploadDocument($booking->bookingID, 'bukti_bayar', $request->file('doc_bukti_bayar'));

            if ($request->hasFile('doc_id_pln')) {
                $this->uploadDocument($booking->bookingID, 'id_pln', $request->file('doc_id_pln'));
            }

            if ($request->hasFile('doc_npwp')) {
                $this->uploadDocument($booking->bookingID, 'npwp', $request->file('doc_npwp'));
            }
        });

        // ⚠️ GANTI: redirect ke halaman tracking booking setelah web native selesai
        return redirect('/cek-booking')->with('success', 'Booking berhasil dikirim. Kode booking akan dikirim ke WhatsApp kamu.');
    }

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------

    private function uploadDocument(string $bookingID, string $docType, $file): void
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('documents', $filename, 'private');

        Document::create([
            'bookingID'  => $bookingID,
            'doc_type'   => $docType,
            'file_path'  => $path,
            'is_primary' => false,
            'order'      => 0,
        ]);
    }

    private function resolveDayType(\Carbon\Carbon $date): string
    {
        $dayOfWeek = $date->dayOfWeek;

        // ⚠️ GANTI: tambahkan logika hari libur nasional kalau sudah ada tabelnya
        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            return 'weekend';
        }

        return 'weekday';
    }
}