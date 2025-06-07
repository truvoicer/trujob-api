<?php

use App\Enums\Product\Category\Business\BusinessProperty;
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
        Schema::create('business_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string(BusinessProperty::LOCATION->value)->nullable();
            $table->string(BusinessProperty::LATITUDE->value)->nullable();
            $table->string(BusinessProperty::LONGITUDE->value)->nullable();
            $table->string(BusinessProperty::WEBSITE->value)->nullable();
            $table->string(BusinessProperty::PHONE->value)->nullable();
            $table->string(BusinessProperty::EMAIL->value)->nullable();
            $table->string(BusinessProperty::OPENING_HOURS->value)->nullable();
            $table->string(BusinessProperty::CLOSING_HOURS->value)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_products');
    }
};
