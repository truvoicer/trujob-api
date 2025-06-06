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
        Schema::create('default_tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rate_id')
                ->constrained('tax_rates')
                ->cascadeOnDelete()
                ->comment('Foreign key to tax rates table');
            $table->timestamps();
            // $table->unique(['tax_rate_id'], 'unique_product_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_tax_rates');
    }
};
