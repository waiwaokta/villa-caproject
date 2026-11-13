<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminUser extends Command
{
    protected $signature = 'make:admin {name} {email} {password}';

    protected $description = 'Buat user baru dengan role admin';

    public function handle(): void
    {
        User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
            'role' => 'admin',
        ]);

        $this->info('User admin berhasil dibuat: ' . $this->argument('email'));
    }
}