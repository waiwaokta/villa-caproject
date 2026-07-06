<?php
namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Wisma;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
class WismaController extends Controller
{
    public function show(string $wismaID, Request $request) // ditambahkan — Request untuk baca query tanggal
    {
        $wisma = Wisma::with(['wismaPhotos', 'prices', 'facilities'])
            ->where('wismaID', $wismaID)
            ->where('is_active', true)
            ->has('wismaPhotos')
            ->firstOrFail();
        $availability = $this->getAvailability($wismaID);

        $prefillCheckIn  = $request->query('check_in'); // ditambahkan — dibawa dari /cari, null kalau akses langsung
        $prefillCheckOut = $request->query('check_out'); // ditambahkan

        return view('wisma.show', compact('wisma', 'availability', 'prefillCheckIn', 'prefillCheckOut')); // diubah — tambah 2 variabel baru
    }
    public function search(Request $request)
    {
        $checkIn  = $request->filled('check_in') ? Carbon::parse($request->check_in) : Carbon::today();
        $checkOut = $request->filled('check_out') ? Carbon::parse($request->check_out) : Carbon::tomorrow();
        $wismas = Wisma::with(['primaryPhoto', 'prices'])
            ->where('is_active', true)
            ->has('wismaPhotos')
            ->when($request->filled('lokasi'), function ($q) use ($request) {
                $q->where('location', 'like', '%' . $request->lokasi . '%');
            })
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['pending', 'approved'])
                  ->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
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
        return $occupiedDates;
    }
}