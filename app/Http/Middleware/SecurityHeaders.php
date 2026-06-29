<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set(
            'X-Content-Type-Options', 'nosniff'
        );
        $response->headers->set(
            'X-Frame-Options', 'SAMEORIGIN'
        );
        $response->headers->set(
            'X-XSS-Protection', '1; mode=block'
        );
        $response->headers->set(
            'Referrer-Policy', 'strict-origin-when-cross-origin'
        );
        $response->headers->set(
            'Permissions-Policy', 'camera=(), microphone=(), geolocation=()'
        );
        $response->headers->set(
            'Strict-Transport-Security', 'max-age=31536000; includeSubDomains'
        );

        // CSP — izinkan asset lokal + Filament + font Google
        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:* http://127.0.0.1:*",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net http://localhost:* http://127.0.0.1:*",
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net http://localhost:* http://127.0.0.1:*",
                "img-src 'self' data: blob:",
                "connect-src 'self' http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*",
                "frame-ancestors 'none'",
            ])
        );

        return $response;
    }
}