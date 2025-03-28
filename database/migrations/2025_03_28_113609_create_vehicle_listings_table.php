<?php

use App\Enums\Listing\Type\Vehicle\VehicleProperty;
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
        Schema::create('vehicle_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string(VehicleProperty::TYPE->value)->nullable();
            $table->string(VehicleProperty::MAKE->value)->nullable();
            $table->string(VehicleProperty::MODEL->value)->nullable();
            $table->string(VehicleProperty::YEAR->value)->nullable();
            $table->string(VehicleProperty::COLOUR->value)->nullable();
            $table->string(VehicleProperty::MILEAGE->value)->nullable();
            $table->string(VehicleProperty::VIN->value)->nullable();
            $table->string(VehicleProperty::PLATE_NUMBER->value)->nullable();
            $table->string(VehicleProperty::ENGINE_SIZE->value)->nullable();
            $table->string(VehicleProperty::TRANSMISSION->value)->nullable();
            $table->string(VehicleProperty::FUEL_TYPE->value)->nullable();
            $table->string(VehicleProperty::CONDITION->value)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_listings');
    }
};
