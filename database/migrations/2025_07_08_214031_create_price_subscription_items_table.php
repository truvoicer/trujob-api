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
        Schema::create('price_subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_subscription_id')
                ->constrained('price_subscriptions')
                ->onDelete('cascade');

            $table->string('frequency_interval_unit');
            $table->integer('frequency_interval_count');
            $table->string('tenure_type'); // trial or regular
            $table->integer('sequence');
            $table->integer('total_cycles');
            $table->decimal('price_value', 19, 4);
            $table->foreignId('price_currency_id')
                ->constrained('currencies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_subscription_items');
    }
};
