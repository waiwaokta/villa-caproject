<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villa_facility', function (Blueprint $table) {
            $table->uuid('villaID');
            $table->uuid('facilityID');

            $table->foreign('villaID')
                ->references('villaID')->on('villas')
                ->onDelete('cascade');

            $table->foreign('facilityID')
                ->references('facilityID')->on('facilities')
                ->onDelete('cascade');

            $table->primary(['villaID', 'facilityID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_facility');
    }
};