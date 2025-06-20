<?php

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
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
            $table->boolean('weight_limit')->default(false);
            $table->enum(
                'weight_unit',
                array_map(fn(ShippingWeightUnit $unit) => $unit->value, ShippingWeightUnit::cases())
            )->nullable()->default(ShippingWeightUnit::KG->value);
            $table->decimal('min_weight', 10, 2)->nullable()->default(0);
            $table->decimal('max_weight', 10, 2)->nullable()->default(0);

            $table->boolean('height_limit')->default(false);
            $table->enum(
                'height_unit',
                array_map(fn(ShippingUnit $unit) => $unit->value, ShippingUnit::cases())
            )->nullable()->default(ShippingUnit::CM->value);
            $table->decimal('min_height', 10, 2)->nullable()->default(0);
            $table->decimal('max_height', 10, 2)->nullable()->default(0);

            $table->boolean('width_limit')->default(false);
            $table->enum(
                'width_unit',
                array_map(fn(ShippingUnit $unit) => $unit->value, ShippingUnit::cases())
            )->nullable()->default(ShippingUnit::CM->value);
            $table->decimal('min_width', 10, 2)->nullable()->default(0);
            $table->decimal('max_width', 10, 2)->nullable()->default(0);

            $table->boolean('length_limit')->default(false);
            $table->enum(
                'length_unit',
                array_map(fn(ShippingUnit $unit) => $unit->value, ShippingUnit::cases())
            )->nullable()->default(ShippingUnit::CM->value);
            $table->decimal('min_length', 10, 2)->nullable()->default(0);
            $table->decimal('max_length', 10, 2)->nullable()->default(0);

            $table->decimal('amount', 10, 2);
            $table->boolean('is_free_shipping_possible')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['shipping_zone_id', 'type', 'min_weight', 'max_weight', 'min_height', 'max_height', 'min_width', 'max_width', 'min_length', 'max_length'], 'idx_shipping_zone_weight_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_rates');
    }
};
