<?php

use App\Enums\Product\Category\RealEstate\RealEstateProperty;
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
        Schema::create('real_estate_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string(RealEstateProperty::BEDROOMS->value)->nullable();
            $table->string(RealEstateProperty::BATHROOMS->value)->nullable();
            $table->string(RealEstateProperty::SIZE->value)->nullable();
            $table->string(RealEstateProperty::TYPE->value)->nullable();
            $table->boolean(RealEstateProperty::FURNISHED->value)->nullable()->default(false);
            $table->boolean(RealEstateProperty::PARKING->value)->nullable()->default(false);
            $table->boolean(RealEstateProperty::PETS_ALLOWED->value)->nullable()->default(false);
            $table->boolean(RealEstateProperty::SMOKE_ALLOWED->value)->nullable()->default(false);
            $table->dateTime(RealEstateProperty::CONSTRUCTION_YEAR->value)->nullable();
            $table->string(RealEstateProperty::ENERGY_EFFICIENCY->value)->nullable();
            $table->string(RealEstateProperty::HEATING->value)->nullable();
            $table->string(RealEstateProperty::COOLING->value)->nullable();
            $table->string(RealEstateProperty::FLOORING->value)->nullable();
            $table->string(RealEstateProperty::AMENITIES->value)->nullable();
            $table->string(RealEstateProperty::VIEW->value)->nullable();
            $table->boolean(RealEstateProperty::BALCONY->value)->nullable()->default(false);
            $table->boolean(RealEstateProperty::GARDEN->value)->nullable()->default(false);
            $table->boolean(RealEstateProperty::POOL->value)->nullable()->default(false);
            $table->boolean(RealEstateProperty::GARAGE->value)->nullable()->default(false);
            $table->boolean(RealEstateProperty::BASEMENT->value)->nullable()->default(false);
            $table->string(RealEstateProperty::ROOF_TYPE->value)->nullable();
            $table->string(RealEstateProperty::LOT_SIZE->value)->nullable();
            $table->string(RealEstateProperty::PROPERTY_TAX->value)->nullable();
            $table->string(RealEstateProperty::HOA_FEES->value)->nullable();
            $table->string(RealEstateProperty::ZONING->value)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_products');
    }
};
