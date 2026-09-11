<?php
namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Villa;
use App\Models\Maintenance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
class VillaController extends Controller
{
    public function show(string $villaID, Request $request)
    {
        $villa = Villa::with(['villaPhotos', 'prices', 'facilities'])
            ->where('villaID', $villaID)
            ->where('is_active', true)
            ->has('villaPhotos')
            ->firstOrFail();
        $availability = $this->getAvailability($villaID);

        $prefillCheckIn  = $request->query('check_in'); 
        $prefillCheckOut = $request->query('check_out'); 

        return view('villa.show', compact('villa', 'availability', 'prefillCheckIn', 'prefillCheckOut'));
    }
    public function search(Request $request)
    {
        $checkIn  = $request->filled('check_in') ? Carbon::parse($request->check_in) : Carbon::today();
        $checkOut = $request->filled('check_out') ? Carbon::parse($request->check_out) : Carbon::tomorrow();

        // Cek dulu apakah ada maintenance block yang berlaku untuk SEMUA villa di rentang ini
        $isGlobalBlocked = Maintenance::whereNull('villaID')
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->exists();

        $villas = Villa::with(['primaryPhoto', 'prices'])
            ->where('is_active', true)
            ->has('villaPhotos')
            ->when($request->filled('lokasi'), function ($q) use ($request) {
                $q->where('location', 'like', '%' . $request->lokasi . '%');
            })
            ->when(!$isGlobalBlocked, function ($q) use ($checkIn, $checkOut) { // skip filter maintenance kalau sudah pasti semua villa diblok (hasil kosong)
                $q->whereDoesntHave('bookings', function ($q2) use ($checkIn, $checkOut) {
                        $q2->whereIn('status', ['pending', 'approved'])
                        ->where('check_in', '<', $checkOut)
                        ->where('check_out', '>', $checkIn);
                    })
                    ->whereDoesntHave('Maintenance', function ($q2) use ($checkIn, $checkOut) {
                        $q2->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()]);
                    });
            }, function ($q) { // kalau global blocked, langsung kosongkan hasil
                $q->whereRaw('1 = 0');
            })
            ->get();

        $lokasi = Villa::where('is_active', true)
            ->has('villaPhotos')
            ->whereNotNull('location')
            ->distinct()
            ->pluck('location');

        $rekomendasi = collect();
            if ($villas->isEmpty()) {
                $rekomendasi = Villa::with(['primaryPhoto', 'prices'])
                    ->where('is_active', true)
                    ->has('villaPhotos')
                    ->inRandomOrder()
                    ->limit(8)
                    ->get();
            }

        return view('villa.villa', [
            'villas'    => $villas,
            'lokasi'    => $lokasi,
            'checkIn'   => $checkIn->toDateString(),
            'checkOut'  => $checkOut->toDateString(),
            'lokasiTerpilih' => $request->lokasi ?? '',
            'rekomendasi' => $rekomendasi,
            'sudahSearch' => $request->filled('check_in') || $request->filled('lokasi'),
        ]);
    }
    private function getAvailability(string $villaID): array
        {
            $bookings = Booking::where('villaID', $villaID)
                ->whereIn('status', ['pending', 'approved'])
                ->where('check_out', '>=', Carbon::today())
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

            // gabungkan tanggal maintenance (khusus villa ini + yang berlaku semua villa)
            $maintenanceDates = Maintenance::where(function ($q) use ($villaID) {
                    $q->where('villaID', $villaID)
                    ->orWhereNull('villaID');
                })
                ->where('date', '>=', Carbon::today())
                ->pluck('date');

            foreach ($maintenanceDates as $date) {
                $occupiedDates[Carbon::parse($date)->toDateString()] = true;
            }

            return $occupiedDates;
        }
}