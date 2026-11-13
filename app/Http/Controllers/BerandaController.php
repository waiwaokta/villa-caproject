<?php
namespace App\Http\Controllers;
use App\Models\Villa;
use App\Models\VillaPhoto;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; 

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $villas = Villa::with(['primaryPhoto', 'prices'])
        ->where('is_active', true)
        ->has('villaPhotos')
        ->when($request->filled('lokasi'), function($q) use ($request) {
            $q->where('location', 'like', '%' . $request->lokasi . '%');
        })
        ->get();
    $lokasi = Villa::where('is_active', true)
        ->has('villaPhotos')
        ->whereNotNull('location')
        ->distinct()
        ->pluck('location');

    $bookingStats = $this->getBookingStats(); 

    return view('beranda', compact('villas', 'lokasi', 'bookingStats')); 
    }

    // Serve foto villa — private storage
    public function foto(string $photoID)
    {
        $photo = VillaPhoto::findOrFail($photoID);
        $path  = storage_path('app/private/' . $photo->file_path);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    }

    //Statistik jumlah booking approved per villa — dihitung dari check_in yang jatuh di bulan berjalan (reset otomatis tiap tanggal 1)
    private function getBookingStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        return Villa::where('is_active', true)
            ->has('villaPhotos')
            ->withCount(['bookings' => function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('status', 'approved')
                  ->whereBetween('check_in', [$startOfMonth, $endOfMonth]);
            }])
            ->get(['villaID', 'name']);
    }
}