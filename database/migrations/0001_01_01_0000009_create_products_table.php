<?php

use App\Enums\Product\ProductType;
use App\Enums\Product\ProductWeightUnit;
use App\Enums\Product\ProductUnit;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum(
                'type',
                array_map(fn(ProductType $unit) => $unit->value, ProductType::cases())
            );

            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('allow_offers')->default(false);
            $table->integer('quantity')->default(1);
            $table->string('sku')->nullable();
            $table->boolean('has_weight')->default(false);
            $table->enum(
                'weight_unit',
                array_map(fn(ProductWeightUnit $unit) => $unit->value, ProductWeightUnit::cases())
            )->nullable()->default(ProductWeightUnit::KG->value);
            $table->decimal('weight', 10, 2)->nullable()->default(0);

            $table->boolean('has_height')->default(false);
            $table->enum(
                'height_unit',
                array_map(fn(ProductUnit $unit) => $unit->value, ProductUnit::cases())
            )->nullable()->default(ProductUnit::CM->value);
            $table->decimal('height', 10, 2)->nullable()->default(0);

            $table->boolean('has_width')->default(false);
            $table->enum(
                'width_unit',
                array_map(fn(ProductUnit $unit) => $unit->value, ProductUnit::cases())
            )->nullable()->default(ProductUnit::CM->value);
            $table->decimal('width', 10, 2)->nullable()->default(0);

            $table->boolean('has_depth')->default(false);
            $table->enum(
                'depth_unit',
                array_map(fn(ProductUnit $unit) => $unit->value, ProductUnit::cases())
            )->nullable()->default(ProductUnit::CM->value);
            $table->decimal('depth', 10, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
