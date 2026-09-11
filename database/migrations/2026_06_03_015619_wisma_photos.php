<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villa_photos', function (Blueprint $table) {
            $table->uuid('photoID')->primary();
            $table->uuid('villaID');
            $table->string('file_path', 255);
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('villaID')->references('villaID')->on('villas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_photos');
    }
};
