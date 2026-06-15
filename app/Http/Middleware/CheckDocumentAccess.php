<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDocumentAccess
{
    public function handle(Request $request, Closure $next)
    {
        $document = Document::findOrFail($request->route('documentID'));
        $booking  = $document->booking;

        // Admin boleh akses semua
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Customer hanya boleh akses dokumen miliknya
        if (Auth::check() && $booking->user_id === Auth::id()) {
            return $next($request);
        }

        abort(403, 'Akses ditolak.');
    }
}