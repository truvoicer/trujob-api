<?php

use App\Enums\Order\Shipping\ShippingRateType;
use App\Models\ShippingRate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->enum(
                'type',
                array_map(fn(ShippingRateType $rate) => $rate->value, ShippingRateType::cases())
            );
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_free_shipping_possible')->default(false);
            $table->timestamps();

            $table->index(['shipping_zone_id', 'rate_type', 'min_amount', 'max_amount'], 'idx_shipping_zone_amount_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_rates');
    }
};
