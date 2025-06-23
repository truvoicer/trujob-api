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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., English, Spanish
            $table->string('iso639_1', 2)->unique()->nullable(); // e.g., en, es (ISO 639-1)
            $table->string('iso639_2', 3)->unique()->nullable(); // e.g., eng, spa (ISO 639-2)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
