<?php

use App\Enums\Product\Category\Pet\PetProperty;
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
        Schema::create('pet_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string(PetProperty::TYPE->value)->nullable();
            $table->string(PetProperty::BREED->value)->nullable();
            $table->string(PetProperty::AGE->value)->nullable();
            $table->string(PetProperty::COLOR->value)->nullable();
            $table->string(PetProperty::SIZE->value)->nullable();
            $table->string(PetProperty::WEIGHT->value)->nullable();
            $table->string(PetProperty::HEALTH_STATUS->value)->nullable();
            $table->string(PetProperty::VACCINATION_STATUS->value)->nullable();
            $table->string(PetProperty::MICROCHIP_STATUS->value)->nullable();
            $table->string(PetProperty::SPAYED_OR_NEUTERED->value)->nullable();
            $table->string(PetProperty::TRAINING_STATUS->value)->nullable();
            $table->string(PetProperty::TEMPERAMENT->value)->nullable();
            $table->text(PetProperty::SPECIAL_NEEDS->value)->nullable();
            $table->string(PetProperty::ADOPTION_FEE->value)->nullable();
            $table->string(PetProperty::AVAILABILITY->value)->nullable();
            $table->boolean(PetProperty::IS_RESCUE->value)->nullable();
            $table->boolean(PetProperty::IS_FOSTER->value)->nullable();
            $table->boolean(PetProperty::IS_ADOPTED->value)->nullable();
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_ADOPTION->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_FOSTER->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_RESERVE->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_SALE->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_BREEDING->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_STUD->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_WALKING->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_BOARDING->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_PET_SITTING->value)->nullable()->default(false);
            $table->boolean(PetProperty::IS_AVAILABLE_FOR_PET_TRAINING->value)->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_products');
    }
};
