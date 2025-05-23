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
        Schema::create('listing_payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')
                ->constrained('listings')
                ->cascadeOnDelete();
            $table->foreignId('payment_gateway_id')
                ->constrained('payment_gateways')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_payment_gateways');
    }
};
