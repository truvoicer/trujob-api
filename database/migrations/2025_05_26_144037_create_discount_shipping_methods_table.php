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
        Schema::create('discount_shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')
                ->constrained('discounts')
                ->cascadeOnDelete();
            $table->foreignId('shipping_method_id')
                ->constrained('shipping_methods')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_shipping_methods');
    }
};
