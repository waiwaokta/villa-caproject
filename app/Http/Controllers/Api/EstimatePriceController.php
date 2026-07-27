<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Price;
use App\Models\Wisma;
use App\Services\DayTypeResolver; 
use App\Services\HolidayService; 
use App\Services\MaintenanceService; 
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
class EstimatePriceController extends Controller
{
    public function __construct(
        private HolidayService $holidayService,
        private DayTypeResolver $dayTypeResolver,
        private MaintenanceService $maintenanceService
    ) {}

    public function calculate(Request $request)
    {
        $request->validate([
            'wismaID'   => 'required|string|exists:wismas,wismaID',
            'user_type' => 'required|in:pln,umum',
            'check_in'  => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);
        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights   = $checkIn->diffInDays($checkOut);
        if ($nights < 1) {
            return response()->json(['error' => 'Pilih tanggal menginap.'], 422);
        }

        // ⚠️ MAINTENANCE CHECK 
        if ($this->maintenanceService->isWismaBlocked($request->wismaID, $checkIn, $checkOut)) {
            return response()->json(['error' => 'Wisma dalam masa pemeliharaan pada tanggal yang dipilih.'], 422);
        }

        // ⚠️ AVAILABILITY CHECK — ditambahkan, cek overlap booking lain
        $isOverlap = Booking::where('wismaID', $request->wismaID)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            })
            ->exists();

        if ($isOverlap) {
            return response()->json(['error' => 'Tanggal yang dipilih sudah dibooking.'], 422);
        }

        $prices = Price::where('wismaID', $request->wismaID)
            ->where('user_type', $request->user_type)
            ->get()
            ->keyBy('day_type');
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        $holidayDates = $this->holidayService->getHolidayDatesInRange($checkIn, $checkOut);

        $breakdown = [];
        $total     = 0;
        foreach ($period as $date) {
            $dayType = $this->dayTypeResolver->resolve($date, $holidayDates);
            if (!isset($prices[$dayType])) {
                return response()->json([
                    'error' => "Harga untuk wisma ini belum lengkap (tipe hari: {$dayType})."
                ], 422);
            }
            $price = (float) $prices[$dayType]->price;
            $total += $price;
            $breakdown[] = [
                'date'     => $date->toDateString(),
                'day_name' => $date->translatedFormat('l, d M Y'),
                'day_type' => $dayType,
                'price'    => $price,
            ];
        }
        return response()->json([
            'nights'    => $nights,
            'total'     => $total,
            'breakdown' => $breakdown,
        ]);
    }
}