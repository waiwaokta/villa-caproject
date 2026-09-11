<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!app()->runningInConsole()) {
            $request = request();

            $host = $request->header('X-Forwarded-Host');
            $scheme = $request->header('X-Forwarded-Proto') ?? $request->getScheme();

            if ($host) {
                URL::forceRootUrl($scheme . '://' . $host);
            }
            // kalau gak ada X-Forwarded-Host, biarin default APP_URL yang dipakai, gak usah override
        }
    }
}