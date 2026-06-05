<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('filament.admin.auth.login')
                ->with('filament.notifications', [
                [
                    'id'      => uniqid(),
                    'title'   => 'Akses Ditolak',
                    'body'    => 'Akun ini tidak memiliki akses admin.',
                    'status'  => 'danger',
                    'duration'=> 6000,
                ]
            ]);
        }

        return $next($request);
    }
}