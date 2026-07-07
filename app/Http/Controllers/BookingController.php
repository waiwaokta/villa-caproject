<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Document;
use App\Models\Price;
use App\Models\Wisma;
use App\Services\DayTypeResolver; 
use App\Services\FonnteService;
use App\Services\HolidayService; 
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private HolidayService $holidayService, 
        private DayTypeResolver $dayTypeResolver 
    ) {}

    public function create(string $wismaID, Request $request)
    {
        $wisma = Wisma::with(['prices'])
            ->where('wismaID', $wismaID)
            ->where('is_active', true)
            ->firstOrFail();

        $prefillCheckIn  = $request->query('check_in');
        $prefillCheckOut = $request->query('check_out');

        return view('booking.form', compact('wisma', 'prefillCheckIn', 'prefillCheckOut'));
    }
    public function store(StoreBookingRequest $request)
    {
        $wisma = Wisma::where('wismaID', $request->wismaID)
            ->where('is_active', true)
            ->firstOrFail();

        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights   = $checkIn->diffInDays($checkOut);

        if ($nights < 1) {
            return back()->withErrors(['check_out' => 'Minimal menginap 1 malam.']);
        }

        // ⚠️ AVAILABILITY CHECK — cek tanggal sudah di-booking orang lain atau belum
        // pending DAN approved sama-sama lock tanggal, rejected dianggap available
        $isOverlap = Booking::where('wismaID', $wisma->wismaID)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            })
            ->exists();

        if ($isOverlap) {
            return back()->withErrors([
                'check_in' => 'Tanggal yang dipilih sudah dibooking. Silakan pilih tanggal lain.',
            ])->withInput();
        }

        // Hitung total price PER MALAM berdasarkan klasifikasi hari masing-masing
        $totalPrice = $this->calculateTotalPrice(
            $wisma->wismaID,
            $request->user_type,
            $checkIn,
            $checkOut
        );

        
        $booking = DB::transaction(function () use ($request, $wisma, $nights, $totalPrice,) {

        $booking = Booking::create([
            'user_id'      => Auth::id(),
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

        $this->uploadDocument($booking->bookingID, 'bukti_bayar', $request->file('doc_bukti_bayar'));

        if ($request->hasFile('doc_ktp')) {
            $this->uploadDocument($booking->bookingID, 'ktp', $request->file('doc_ktp'));
        }

        if ($request->hasFile('doc_npwp')) {
            $this->uploadDocument($booking->bookingID, 'npwp', $request->file('doc_npwp'));
        }

        if ($request->hasFile('doc_id_pln')) {
            $this->uploadDocument($booking->bookingID, 'id_pln', $request->file('doc_id_pln'));
        }

        // PLN: KTP atau NPWP, simpan sesuai mana yang diisi
        if ($request->hasFile('doc_ktp_pln')) {
            $this->uploadDocument($booking->bookingID, 'ktp', $request->file('doc_ktp_pln'));
        }
        return $booking;
    });

        app(FonnteService::class)->sendNewBookingAlert($booking);
        return redirect('/cek-booking')->with('success', 'Booking berhasil dikirim. Kode booking akan dikirim ke WhatsApp kamu.');
    }

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------
    private function calculateTotalPrice(string $wismaID, string $userType, Carbon $checkIn, Carbon $checkOut): float
    {
        // Ambil semua harga wisma ini sekali saja — hindari query berulang per malam
        $prices = Price::where('wismaID', $wismaID)
            ->where('user_type', $userType)
            ->get()
            ->keyBy('day_type');

        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        // diubah — pakai HolidayService, bukan query Holiday langsung di sini
        $holidayDates = $this->holidayService->getHolidayDatesInRange($checkIn, $checkOut);

        $total = 0;

        foreach ($period as $date) {
            $dayType = $this->dayTypeResolver->resolve($date, $holidayDates); 

            if (!isset($prices[$dayType])) {
                throw new \RuntimeException("Harga untuk tipe hari '{$dayType}' belum diatur untuk wisma ini.");
            }

            $total += (float) $prices[$dayType]->price;
        }

        return $total;
    }

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
}