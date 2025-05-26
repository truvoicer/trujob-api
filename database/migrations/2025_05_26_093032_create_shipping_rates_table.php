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
                'rate_type',
                array_map(fn(ShippingRateType $rate) => $rate->value, ShippingRateType::cases())
            );
            $table->decimal('min_value', 10, 2)->nullable();
            $table->decimal('max_value', 10, 2)->nullable();
            $table->decimal('rate_amount', 10, 2);
            $table->boolean('is_free_shipping_possible')->default(false);
            $table->timestamps();

            $table->index(['shipping_zone_id', 'rate_type', 'min_value', 'max_value'], 'idx_shipping_zone_rate_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_rates');
    }
};
