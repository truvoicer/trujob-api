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
            $table->foreignId('shipping_zone_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->cascadeOnDelete();
            $table->enum(
                'type',
                array_map(fn(ShippingRateType $rate) => $rate->value, ShippingRateType::cases())
            );

            $table->string('name')->unique();
            $table->string('label');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->boolean('has_weight')->default(false);
            $table->boolean('has_height')->default(false);
            $table->boolean('has_width')->default(false);
            $table->boolean('has_depth')->default(false);

            $table->boolean('has_max_dimension')->default(false);
            $table->decimal('max_dimension', 10, 2)->nullable();
            $table->enum(
                'max_dimension_unit',
                array_map(fn(ShippingUnit $unit) => $unit->value, ShippingUnit::cases())
            )->nullable()->default(ShippingUnit::CM->value);

            $table->enum(
                'weight_unit',
                array_map(fn(ShippingWeightUnit $unit) => $unit->value, ShippingWeightUnit::cases())
            )->nullable()->default(ShippingWeightUnit::KG->value);
            $table->decimal('max_weight', 10, 2)->nullable();

            $table->enum(
                'height_unit',
                array_map(fn(ShippingUnit $unit) => $unit->value, ShippingUnit::cases())
            )->nullable()->default(ShippingUnit::CM->value);
            $table->decimal('max_height', 10, 2)->nullable();

            $table->enum(
                'width_unit',
                array_map(fn(ShippingUnit $unit) => $unit->value, ShippingUnit::cases())
            )->nullable()->default(ShippingUnit::CM->value);
            $table->decimal('max_width', 10, 2)->nullable();

            $table->enum(
                'depth_unit',
                array_map(fn(ShippingUnit $unit) => $unit->value, ShippingUnit::cases())
            )->nullable()->default(ShippingUnit::CM->value);
            $table->decimal('max_depth', 10, 2)->nullable();

            $table->decimal('amount', 10, 2)->nullable()->default(0);

            $table->decimal('dimensional_weight_divisor', 10, 2)->nullable();

            $table->timestamps();

            $table->index([
                'shipping_method_id',
                'shipping_zone_id',
                'currency_id',
                'is_active',
                'max_dimension',
                'max_dimension_unit',
                'max_weight',
                'weight_unit',
                'max_height',
                'height_unit',
                'max_width',
                'width_unit',
                'max_depth',
                'depth_unit',
                'amount',
                'dimensional_weight_divisor',
            ], 'idx_shipping_method_shipping_rates');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_rates');
    }
};
