<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\CheckDocumentAccess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'check.document.access' => CheckDocumentAccess::class,
            'role'                  => EnsureUserIsAdmin::class,
        ]);

        // Security headers — semua response web
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->booted(function () {

        // Rate limit booking — 3x per menit per IP
        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Terlalu banyak percobaan. Coba lagi dalam 1 menit.'
                    ], 429);
                });
        });

        // Rate limit cek-booking — 10x per menit per IP
        RateLimiter::for('cek-booking', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Terlalu banyak percobaan. Coba lagi dalam 1 menit.'
                    ], 429);
                });
        });

    })->create();