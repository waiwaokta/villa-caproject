<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wisma_photos', function (Blueprint $table) {
            $table->uuid('photoID')->primary();
            $table->uuid('wismaID');
            $table->string('file_path', 255);
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('wismaID')->references('wismaID')->on('wismas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wisma_photos');
    }
};
