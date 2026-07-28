<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\WismaController;
use App\Http\Controllers\Api\EstimatePriceController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return redirect('/admin');
});

// Cek booking — rate limit 10x per menit
// ⚠️ GANTI: tambah CekBookingController setelah web native selesai
Route::get('/cek-booking', function () {
    return view('cek-booking');
})->middleware('throttle:cek-booking')
  ->name('booking.cek');

// Document routes — private access
Route::middleware(['auth'])->group(function () {
    Route::get(
        '/dokumen/{documentID}',
        [DocumentController::class, 'show']
    )->middleware('check.document.access');

    Route::delete(
        '/dokumen/{documentID}',
        [DocumentController::class, 'destroy']
    )->middleware('role:admin');
});

// Document routes — private access
Route::middleware(['auth'])->group(function () {
    Route::get(
        '/dokumen/{documentID}',
        [DocumentController::class, 'show']
    )->middleware('check.document.access');

Route::delete(
    '/dokumen/{documentID}',
    [DocumentController::class, 'destroy']
)->middleware('role:admin');
});

// Tambahkan di dalam atau di luar middleware auth — 
// karena guest booking diizinkan (user_id nullable)
Route::post('/booking', [BookingController::class, 'store'])
    ->middleware('throttle:3,1') // max 3x per menit, security
    ->name('booking.store');

// Beranda
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// Detail Wisma
Route::get('/wisma/{wismaID}', [WismaController::class, 'show'])
    ->name('wisma.show');

// Foto wisma — public (tidak butuh auth, tapi via controller bukan URL langsung)
Route::get('/foto/{photoID}', [BerandaController::class, 'foto'])
    ->name('dokumen.foto');

// Booking
Route::post('/booking', [BookingController::class, 'store'])
    ->middleware('throttle:booking')
    ->name('booking.store');

// Cek booking
Route::get('/cek-booking', function () {
    return view('booking.check-book');
})->middleware('throttle:cek-booking')
  ->name('booking.cek');

// Document routes — private access
Route::middleware(['auth'])->group(function () {
    Route::get('/dokumen/{documentID}', [DocumentController::class, 'show'])
        ->middleware('check.document.access');
    Route::delete('/dokumen/{documentID}', [DocumentController::class, 'destroy'])
        ->middleware('role:admin');
});

Route::get('/api/estimate-price', [EstimatePriceController::class, 'calculate'])
    ->middleware('throttle:30,1');

Route::get('/booking/{wismaID}', [BookingController::class, 'create'])
    ->name('booking.create');

Route::get('/cari', [WismaController::class, 'search'])
    ->name('wisma.search');

Route::get('/tentang', function () {
    return view('navbar.tentang');
})->name('tentang');

Route::get('/kontak', [ContactController::class, 'index'])->name('kontak');
Route::post('/kontak', [ContactController::class, 'store'])
    ->middleware('throttle:5,1') 
    ->name('kontak.store');