<?php

use App\Enums\Order\OrderStatus;
use App\Enums\Price\PriceType;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users');
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries');
            $table->foreignId('currency_id')
                ->constrained('currencies');
            $table->foreignId('billing_address_id')
                ->nullable()
                ->constrained('addresses');
            $table->foreignId('shipping_address_id')
                ->nullable()
                ->constrained('addresses');
            $table->enum(
                'price_type',
                array_map(fn(PriceType $type) => $type->value, PriceType::cases())
            );
            $table->enum(
                'status',
                array_map(fn(OrderStatus $orderStatus) => $orderStatus->value, OrderStatus::cases())
            )->nullable()->default(OrderStatus::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
