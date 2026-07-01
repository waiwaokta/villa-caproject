<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Price;
use App\Models\Wisma;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class EstimatePriceController extends Controller
{
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
            return response()->json(['error' => 'Minimal menginap 1 malam.'], 422);
        }

        $prices = Price::where('wismaID', $request->wismaID)
            ->where('user_type', $request->user_type)
            ->get()
            ->keyBy('day_type');

        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        $datesInRange = collect($period)->map(fn(Carbon $d) => $d->toDateString());

        $holidayDates = Holiday::whereIn('date', $datesInRange)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $breakdown = [];
        $total     = 0;

        foreach ($period as $date) {
            $dayType = $this->resolveDayType($date, $holidayDates);

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

    private function resolveDayType(Carbon $date, array $holidayDates): string
    {
        if (in_array($date->toDateString(), $holidayDates)) {
            return 'holiday';
        }

        if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
            return 'weekend';
        }

        return 'weekday';
    }
}