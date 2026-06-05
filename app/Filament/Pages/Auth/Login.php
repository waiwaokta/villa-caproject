<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected int $maxLoginAttempts = 5;    // percobaan
    protected int $lockoutDuration = 60;    // durasi kunci (detik)
    protected function getRateLimitedNotificationMessage(): string
    {
        return 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa saat.';
    }

    protected function throwFailureValidationException(): never
    {
        // Cek apakah email terdaftar tapi bukan admin
        $user = \App\Models\User::where('email', $this->data['email'] ?? '')->first();

        if ($user && $user->role !== 'admin') {
            // Sama-in pesan errornya, jangan bocorkan info
            throw ValidationException::withMessages([
                'data.email' => 'Email atau password salah.',
            ]);
        }

        // Email memang tidak terdaftar, pakai pesan default Filament
        throw ValidationException::withMessages([
            'data.email' => 'Email atau password salah.',
        ]);
    }
}