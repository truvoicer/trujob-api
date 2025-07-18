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
        Schema::create('price_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_id')
                  ->constrained('prices')
                  ->onDelete('cascade');

            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->decimal('setup_fee_value', 19, 4)->nullable();
            $table->foreignId('setup_fee_currency_id')
                  ->nullable()
                  ->constrained('currencies')
                  ->nullOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_subscriptions');
    }
};
