<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Booking;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Upload dokumen saat booking submit.
     * Dipanggil dari BookingController, bukan route langsung.
     */
    public function store(StoreDocumentRequest $request)
    {
        $booking = Booking::findOrFail($request->bookingID);

        // Ownership check — hanya pemilik booking yang bisa upload
        if (
            Auth::check() &&
            $booking->user_id !== Auth::id()
        ) {
            abort(403);
        }

        $file     = $request->file('file');
        $hash     = Str::random(40);
        $ext      = $file->getClientOriginalExtension();
        $filename = $hash . '.' . $ext;

        // Simpan ke storage/app/private/documents/
        $path = $file->storeAs('documents', $filename, 'private');

        $document = Document::create([
            'bookingID' => $request->bookingID,
            'doc_type'  => $request->doc_type,
            'file_path' => $path,
            'is_primary'=> false,
            'order'     => 0,
        ]);

        return $document;
    }

    /**
     * Serve file secara private — URL langsung TIDAK bisa diakses.
     * Middleware CheckDocumentAccess sudah memverifikasi ownership sebelum sampai sini.
     */
    public function show(string $documentID)
    {
        $document = Document::findOrFail($documentID);

        $path = storage_path('app/private/' . $document->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($path);
    }

    /**
     * Hapus dokumen — hanya admin.
     */
    public function destroy(string $documentID)
    {
        $document = Document::findOrFail($documentID);

        Storage::disk('private')->delete($document->file_path);
        $document->delete();

        return response()->noContent();
    }
}