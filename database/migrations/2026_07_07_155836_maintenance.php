<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance', function (Blueprint $table) {
            $table->uuid('maintenanceID')->primary();
            $table->uuid('wismaID')->nullable(); 
            $table->date('date');
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->foreign('wismaID')->references('wismaID')->on('wismas')->onDelete('cascade');
            $table->unique(['wismaID', 'date']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};