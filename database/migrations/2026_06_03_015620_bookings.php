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
            $table->uuid('villaID');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('total_nights');
            $table->decimal('total_price', 15, 2);
            $table->string('guest_name', 255);
            $table->string('guest_phone', 15);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reject_desc')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('villaID')->references('villaID')->on('villas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
