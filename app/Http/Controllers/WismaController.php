<?php
namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Wisma;
use App\Models\Maintenance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
class WismaController extends Controller
{
    public function show(string $wismaID, Request $request) 
    {
        $wisma = Wisma::with(['wismaPhotos', 'prices', 'facilities'])
            ->where('wismaID', $wismaID)
            ->where('is_active', true)
            ->has('wismaPhotos')
            ->firstOrFail();
        $availability = $this->getAvailability($wismaID);

        $prefillCheckIn  = $request->query('check_in'); 
        $prefillCheckOut = $request->query('check_out'); 

        return view('wisma.show', compact('wisma', 'availability', 'prefillCheckIn', 'prefillCheckOut')); 
    }
    public function search(Request $request)
    {
        $checkIn  = $request->filled('check_in') ? Carbon::parse($request->check_in) : Carbon::today();
        $checkOut = $request->filled('check_out') ? Carbon::parse($request->check_out) : Carbon::tomorrow();

        // Cek dulu apakah ada maintenance block yang berlaku untuk SEMUA wisma di rentang ini
        $isGlobalBlocked = Maintenance::whereNull('wismaID') 
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->exists();

        $wismas = Wisma::with(['primaryPhoto', 'prices'])
            ->where('is_active', true)
            ->has('wismaPhotos')
            ->when($request->filled('lokasi'), function ($q) use ($request) {
                $q->where('location', 'like', '%' . $request->lokasi . '%');
            })
            ->when(!$isGlobalBlocked, function ($q) use ($checkIn, $checkOut) { // skip filter maintenance kalau sudah pasti semua wisma diblok (hasil kosong)
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

        $lokasi = Wisma::where('is_active', true)
            ->has('wismaPhotos')
            ->whereNotNull('location')
            ->distinct()
            ->pluck('location');

        return view('wisma.wisma', [
            'wismas'    => $wismas,
            'lokasi'    => $lokasi,
            'checkIn'   => $checkIn->toDateString(),
            'checkOut'  => $checkOut->toDateString(),
            'lokasiTerpilih' => $request->lokasi ?? '',
        ]);
    }
    private function getAvailability(string $wismaID): array
        {
            $bookings = Booking::where('wismaID', $wismaID)
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

            // ditambahkan — gabungkan tanggal maintenance (khusus wisma ini + yang berlaku semua wisma)
            $maintenanceDates = Maintenance::where(function ($q) use ($wismaID) {
                    $q->where('wismaID', $wismaID)
                    ->orWhereNull('wismaID');
                })
                ->where('date', '>=', Carbon::today())
                ->pluck('date');

            foreach ($maintenanceDates as $date) {
                $occupiedDates[Carbon::parse($date)->toDateString()] = true;
            }

            return $occupiedDates;
        }
}