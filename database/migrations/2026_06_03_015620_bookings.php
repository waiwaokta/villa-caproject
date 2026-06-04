<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('bookingID')->primary();
            $table->uuid('wismaID');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('total_nights');
            $table->decimal('total_price', 15, 2);
            $table->enum('user_type', ['pln', 'umum']);
            $table->enum('booking_type', ['perorangan', 'instansi']);
            $table->string('guest_name', 255);
            $table->string('guest_phone', 15);
            $table->string('guest_ktp', 16);
            $table->string('employee_id', 20)->nullable();
            $table->string('inst_name', 255)->nullable();
            $table->string('inst_npwp', 20)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reject_desc')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreign('wismaID')->references('wismaID')->on('wismas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
