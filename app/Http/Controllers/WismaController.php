<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Wisma;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WismaController extends Controller
{
    public function show(string $wismaID)
    {
        $wisma = Wisma::with(['wismaPhotos', 'prices', 'facilities'])
            ->where('wismaID', $wismaID)
            ->where('is_active', true)
            ->has('wismaPhotos')
            ->firstOrFail();

        $availability = $this->getAvailability($wismaID);

        return view('wisma.show', compact('wisma', 'availability'));
    }

    /**
     * Generate data availability 3 bulan ke depan untuk kalender.
     * Return array tanggal => true/false (true = sudah dibooking).
     */
    private function getAvailability(string $wismaID): array
    {
        $startDate = Carbon::today();
        $endDate   = Carbon::today()->addMonths(3);

        $bookings = Booking::where('wismaID', $wismaID)
            ->whereIn('status', ['pending', 'approved'])
            ->where('check_out', '>=', $startDate)
            ->where('check_in', '<=', $endDate)
            ->get(['check_in', 'check_out']);

        $occupiedDates = [];

        foreach ($bookings as $booking) {
            $checkIn  = Carbon::parse($booking->check_in);
            $checkOut = Carbon::parse($booking->check_out);

            $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

            foreach ($period as $date) {
                $occupiedDates[$date->toDateString()] = true;
            }
        }

        return $occupiedDates;
    }
}