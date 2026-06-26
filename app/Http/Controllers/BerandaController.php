<?php

namespace App\Http\Controllers;

use App\Models\Wisma;
use App\Models\WismaPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    return view('beranda', compact('wismas', 'lokasi'));
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
}