<?php

use App\Enums\Listing\Type\Food\FoodProperty;
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
        Schema::create('food_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->text(FoodProperty::DIETARY_REQUIREMENTS->value)->nullable();
            $table->text(FoodProperty::ALLERGENS->value)->nullable();
            $table->text(FoodProperty::INGREDIENTS->value)->nullable();
            $table->text(FoodProperty::NUTRITIONAL_INFORMATION->value)->nullable();
            $table->text(FoodProperty::SERVING_SIZE->value)->nullable();
            $table->text(FoodProperty::PACKAGING->value)->nullable();
            $table->text(FoodProperty::STORAGE_REQUIREMENTS->value)->nullable();
            $table->text(FoodProperty::STORAGE->value)->nullable();
            $table->text(FoodProperty::HEALTH_AND_SAFETY->value)->nullable();
            $table->dateTime(FoodProperty::USE_BY_DATE->value)->nullable();
            $table->dateTime(FoodProperty::EXPIRY_DATE->value)->nullable();
            $table->dateTime(FoodProperty::SELL_BY_DATE->value)->nullable();
            $table->dateTime(FoodProperty::MANUFACTURED_DATE->value)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_listings');
    }
};
