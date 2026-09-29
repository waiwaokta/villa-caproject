<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\VillaController;
use App\Http\Controllers\Api\EstimatePriceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CekBookingController;

// Beranda
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// Detail villa
Route::get('/villa/{villaID}', [VillaController::class, 'show'])
    ->name('villa.show');

// Foto villa — public
Route::get('/foto/{photoID}', [BerandaController::class, 'foto'])
    ->name('dokumen.foto');

// Cari villa
Route::get('/cari', [VillaController::class, 'search'])
    ->name('villa.search');

// Booking — form & submit
Route::get('/booking/{villaID}', [BookingController::class, 'create'])
    ->name('booking.create');

Route::post('/booking', [BookingController::class, 'store'])
    ->middleware('throttle:3,1')
    ->name('booking.store');

// Konfirmasi booking berhasil dikirim — DITAMBAHKAN, sebelumnya tidak ada route untuk confirm.blade.php
Route::get('/booking/confirm/{bookingID}', [BookingController::class, 'confirm'])
    ->name('booking.confirm');

// Cek booking — tracking status
Route::get('/cek-booking', [CekBookingController::class, 'index'])
    ->middleware('throttle:cek-booking')
    ->name('booking.cek');

Route::post('/cek-booking', [CekBookingController::class, 'cari'])
    ->middleware('throttle:cek-booking')
    ->name('booking.cek.cari');

// Dokumen — private access (DIHAPUS duplikatnya, sekarang cuma 1 block)
Route::middleware(['auth'])->group(function () {
    Route::get('/dokumen/{documentID}', [DocumentController::class, 'show'])
        ->middleware('check.document.access');
    Route::delete('/dokumen/{documentID}', [DocumentController::class, 'destroy'])
        ->middleware('role:admin');
});

// API estimasi harga
Route::get('/api/estimate-price', [EstimatePriceController::class, 'calculate'])
    ->middleware('throttle:30,1');

// Halaman statis
Route::get('/tentang', function () {
    return view('navbar.tentang');
})->name('tentang');

// Kontak
Route::get('/kontak', [ContactController::class, 'index'])->name('kontak');
Route::post('/kontak', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('kontak.store');