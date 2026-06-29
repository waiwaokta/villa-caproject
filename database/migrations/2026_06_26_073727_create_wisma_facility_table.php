<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wisma_facility', function (Blueprint $table) {
            $table->uuid('wismaID');
            $table->uuid('facilityID');

            $table->foreign('wismaID')
                ->references('wismaID')->on('wismas')
                ->onDelete('cascade');

            $table->foreign('facilityID')
                ->references('facilityID')->on('facilities')
                ->onDelete('cascade');

            $table->primary(['wismaID', 'facilityID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wisma_facility');
    }
};