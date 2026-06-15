<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BookingController;

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