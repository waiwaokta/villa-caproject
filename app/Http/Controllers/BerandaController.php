<?php
namespace App\Http\Controllers;
use App\Models\Wisma;
use App\Models\WismaPhoto;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; 

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $wismas = Wisma::with(['primaryPhoto', 'prices'])
        ->where('is_active', true)
        ->has('wismaPhotos')
        ->when($request->filled('lokasi'), function($q) use ($request) {
            $q->where('location', 'like', '%' . $request->lokasi . '%');
        })
        ->get();
    $lokasi = Wisma::where('is_active', true)
        ->has('wismaPhotos')
        ->whereNotNull('location')
        ->distinct()
        ->pluck('location');

    $bookingStats = $this->getBookingStats(); 

    return view('beranda', compact('wismas', 'lokasi', 'bookingStats'));
    }

    // Serve foto wisma — private storage
    public function foto(string $photoID)
    {
        $photo = WismaPhoto::findOrFail($photoID);
        $path  = storage_path('app/private/' . $photo->file_path);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    }

    //Statistik jumlah booking approved per wisma — dihitung dari check_in yang jatuh di bulan berjalan (reset otomatis tiap tanggal 1)
    private function getBookingStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        return Wisma::where('is_active', true)
            ->has('wismaPhotos')
            ->withCount(['bookings' => function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('status', 'approved')
                  ->whereBetween('check_in', [$startOfMonth, $endOfMonth]);
            }])
            ->get(['wismaID', 'name']);
    }
}