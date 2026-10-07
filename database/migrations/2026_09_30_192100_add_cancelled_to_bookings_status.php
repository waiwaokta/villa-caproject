<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::table('bookings')->where('status', 'cancelled')->update(['status' => 'rejected']); // supaya rollback tidak gagal
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};