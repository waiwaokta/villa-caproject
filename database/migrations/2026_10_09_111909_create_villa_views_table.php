<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('villa_views', function (Blueprint $table) {
            $table->id();
            $table->string('villaID', 36); // sama dengan tipe villas
            $table->string('visitor_hash', 64)->nullable(); // hash id sesi
            $table->timestamp('viewed_at')->useCurrent(); // waktu klik
            $table->index(['villaID', 'viewed_at']); // mempercepat agregasi bulanan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_views');
    }
};
