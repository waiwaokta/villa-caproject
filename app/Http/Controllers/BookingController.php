<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Document;
use App\Models\Price;
use App\Models\Villa;
use App\Services\DayTypeResolver;
use App\Services\FonnteService;
use App\Services\HolidayService;
use App\Services\MaintenanceService;
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
        private DayTypeResolver $dayTypeResolver,
        private MaintenanceService $maintenanceService
    ) {}

    public function create(string $villaID, Request $request)
    {
        $villa = Villa::with(['prices'])
            ->where('villaID', $villaID)
            ->where('is_active', true)
            ->firstOrFail();

        $prefillCheckIn  = $request->query('check_in');
        $prefillCheckOut = $request->query('check_out');

        return view('booking.form', compact('villa', 'prefillCheckIn', 'prefillCheckOut'));
    }
    public function store(StoreBookingRequest $request)
    {
        $villa = Villa::where('villaID', $request->villaID)
            ->where('is_active', true)
            ->firstOrFail();

        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights   = $checkIn->diffInDays($checkOut);

        if ($nights < 1) {
            return back()->withErrors(['check_out' => 'Minimal menginap 1 malam.']);
        }

        // AVAILABILITY CHECK — cek tanggal sudah di-booking orang lain atau belum
        // pending DAN approved sama-sama lock tanggal, rejected dianggap available
        $isOverlap = Booking::where('villaID', $villa->villaID)
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

        // MAINTENANCE CHECK — cek villa sedang di-maintenance di rentang tanggal ini
        if ($this->maintenanceService->isVillaBlocked($villa->villaID, $checkIn, $checkOut)) {
            return back()->withErrors([
                'check_in' => 'Villa sedang dalam masa maintenance pada tanggal yang dipilih. Silakan pilih tanggal lain.',
            ])->withInput();
        }

        // Hitung total price PER MALAM berdasarkan klasifikasi hari masing-masing
        $totalPrice = $this->calculateTotalPrice(
            $villa->villaID,
            $checkIn,
            $checkOut
        ); // keterangan: parameter user_type dihapus, tidak ada lagi pembedaan tipe tamu

        
        $booking = DB::transaction(function () use ($request, $villa, $nights, $totalPrice,) {

        $booking = Booking::create([
            'user_id'      => Auth::id(),
            'villaID'      => $villa->villaID,
            'check_in'     => $request->check_in,
            'check_out'    => $request->check_out,
            'total_nights' => $nights,
            'total_price'  => $totalPrice,
            'guest_name'   => $request->guest_name,
            'guest_phone'  => $request->guest_phone,
            'status'       => 'pending',
        ]); // keterangan: user_type, booking_type, guest_ktp, employee_id, inst_name, inst_npwp dihapus, tidak lagi relevan

        $this->uploadDocument($booking->bookingID, 'bukti_bayar', $request->file('doc_bukti_bayar'));

        if ($request->hasFile('doc_ktp')) {
            $this->uploadDocument($booking->bookingID, 'ktp', $request->file('doc_ktp'));
        }
        // keterangan: logic upload npwp, id_pln, dan ktp_pln dihapus seluruhnya

        return $booking;
    });

        app(FonnteService::class)->sendNewBookingAlert($booking);
        return redirect()->route('booking.confirm', $booking->bookingID);
    }

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------

    private function calculateTotalPrice(string $villaID, Carbon $checkIn, Carbon $checkOut): float
    {
        $prices = Price::where('villaID', $villaID)
            ->get() // keterangan: filter where('user_type', ...) dihapus
            ->keyBy('day_type');

        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        $holidayDates = $this->holidayService->getHolidayDatesInRange($checkIn, $checkOut);

        $total = 0;

        foreach ($period as $date) {
            $dayType = $this->dayTypeResolver->resolve($date, $holidayDates);

            if (!isset($prices[$dayType])) {
                throw new \RuntimeException("Harga untuk tipe hari '{$dayType}' belum diatur untuk villa ini.");
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

    public function confirm(string $bookingID)
    {
        $booking = Booking::with('villa:villaID,name')
            ->select(['bookingID', 'villaID', 'check_in', 'check_out', 'guest_name'])
            ->where('bookingID', $bookingID)
            ->firstOrFail();

        return view('booking.confirm', compact('booking'));
    }
}