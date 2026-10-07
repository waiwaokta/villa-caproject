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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $nights   = (int) $checkIn->diffInDays($checkOut);

        if ($nights < 1) {
            return back()->withErrors(['check_out' => 'Minimal menginap 1 malam.']);
        }

        // AVAILABILITY CHECK — cek tanggal sudah di-booking orang lain atau belum
        $isOverlap = Booking::where('villaID', $villa->villaID)
            ->locking()
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
        );
        $booking = DB::transaction(function () use ($request, $villa, $nights, $totalPrice) {

            $booking = Booking::create([
                'villaID'      => $villa->villaID,
                'check_in'     => $request->check_in,
                'check_out'    => $request->check_out,
                'total_nights' => $nights,
                'total_price'  => $totalPrice,
                'guest_name'   => $request->guest_name,
                'guest_phone'  => $request->guest_phone,
                'status'       => 'pending',
                'expires_at'   => now()->addMinutes(15), // ditambahkan, batas waktu bayar
            ]);

            $this->uploadDocument($booking->bookingID, 'ktp', $request->file('doc_ktp'));

            return $booking;
        });

        // app(FonnteService::class)->sendNewBookingAlert($booking); // dinonaktifkan sementara, pindah ke webhook pembayaran
        return redirect($this->signedUrl('booking.nota', $booking->bookingID)); // diubah, ke halaman nota
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

    private function signedUrl(string $route, string $bookingID, int $minutes = 30): string
    {
        return URL::temporarySignedRoute($route, now()->addMinutes($minutes), ['bookingID' => $bookingID]);
    }

    public function nota(string $bookingID)
    {
        $booking = Booking::with('villa')->where('bookingID', $bookingID)->firstOrFail();

        if ($booking->status === 'approved') {
            return redirect($this->signedUrl('booking.confirm', $bookingID, 1440));
        }
        if ($booking->status !== 'pending' || $booking->expires_at?->isPast()) {
            return redirect('/')->with('error', 'Waktu pembayaran habis, silakan booking ulang.');
        }

        return view('booking.nota', [
            'booking'   => $booking,
            'nights'    => $booking->total_nights,
            'ktpUrl'    => $this->signedUrl('booking.ktp', $bookingID),
            'statusUrl' => $this->signedUrl('booking.status', $bookingID),
            'cancelUrl' => $this->signedUrl('booking.cancel', $bookingID),
            'payUrl'    => $this->signedUrl('booking.pay', $bookingID), // ditambahkan
            'qrUrl'     => $booking->qris_url ? $this->signedUrl('booking.qr', $bookingID) : null, // ditambahkan, QR muncul lagi kalau halaman di-reload
        ]);
    }

    public function pay(string $bookingID, MidtransService $midtrans)
    {
        $booking = Booking::with('villa')->where('bookingID', $bookingID)->firstOrFail();

        if ($booking->status !== 'pending' || $booking->expires_at?->isPast()) {
            return response()->json(['error' => 'Waktu pembayaran sudah habis, silakan booking ulang.'], 410);
        }

        try {
            $midtrans->createQris($booking);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Gagal membuat QRIS, silakan coba lagi.'], 502);
        }

        $booking->refresh();

        return response()->json([
            'qr_url'     => $this->signedUrl('booking.qr', $bookingID),
            'expires_at' => $booking->expires_at->toIso8601String(),
        ]);
    }

    public function qr(string $bookingID)
    {
        $booking = Booking::where('bookingID', $bookingID)->whereNotNull('qris_url')->firstOrFail();

        $png = Http::timeout(10)->get($booking->qris_url);
        abort_unless($png->successful(), 502);

        return response($png->body(), 200, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function cancel(Request $request, string $bookingID, MidtransService $midtrans)
    {
        $booking = Booking::where('bookingID', $bookingID)->where('status', 'pending')->first();

        if ($booking) {
            $midtrans->sync($booking); // jaga-jaga kalau pembayaran baru saja masuk
            $booking->refresh();

            if ($booking->status === 'pending') {
                $booking->update(['status' => 'cancelled']);
                $midtrans->cancelQris($booking);
            }
        }

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : redirect('/')->with('info', 'Booking dibatalkan.');
    }

    public function status(string $bookingID, MidtransService $midtrans)
    {
        $booking = Booking::where('bookingID', $bookingID)->firstOrFail();

        $midtrans->sync($booking); // cek ke Midtrans, tidak perlu webhook untuk demo lokal
        $booking->refresh();

        return response()->json([
            'status'    => $booking->status,
            'redirect'  => $booking->status === 'approved' ? $this->signedUrl('booking.confirm', $bookingID, 1440) : null,
            'qr_active' => (bool) $booking->qris_url, // false kalau QR hangus
        ]);
    }

    public function ktp(string $bookingID)
    {
        $doc = Document::where('bookingID', $bookingID)->where('doc_type', 'ktp')->firstOrFail();

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        abort_unless($disk->exists($doc->file_path), 404);

        return $disk->response($doc->file_path);
    }

    public function confirm(string $bookingID)
    {
        $booking = Booking::with('villa')->where('bookingID', $bookingID)->firstOrFail();
        abort_unless($booking->status === 'approved', 403);

        return view('booking.confirm', [
            'booking'    => $booking,
            'nights'     => $booking->total_nights,
            'receiptUrl' => $this->signedUrl('booking.receipt', $bookingID, 1440),
        ]);
    }

    public function receipt(string $bookingID)
    {
        $booking = Booking::with('villa')->where('bookingID', $bookingID)->firstOrFail();
        abort_unless($booking->status === 'approved', 403); // nota hanya untuk booking yang sudah lunas

        $pdf = Pdf::loadView('booking.receipt', ['booking' => $booking])
            ->setPaper('a5', 'portrait');

        return $pdf->download('Nota-' . $booking->bookingID . '.pdf');
    }
}