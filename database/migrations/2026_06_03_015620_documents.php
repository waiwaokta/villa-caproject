<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('documentID')->primary();
            $table->uuid('bookingID');
            $table->enum('doc_type', ['ktp', 'bukti_bayar']);
            $table->string('file_path', 255);
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('bookingID')->references('bookingID')->on('bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
