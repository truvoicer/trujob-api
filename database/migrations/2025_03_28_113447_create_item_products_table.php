<?php

use App\Enums\Product\Category\Item\ItemProperty;
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
        Schema::create('item_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string(ItemProperty::CONDITION->value)->nullable();
            $table->string(ItemProperty::MODEL->value)->nullable();
            $table->string(ItemProperty::YEAR->value)->nullable();
            $table->string(ItemProperty::COLOR->value)->nullable();
            $table->string(ItemProperty::SIZE->value)->nullable();
            $table->string(ItemProperty::WEIGHT->value)->nullable();
            $table->string(ItemProperty::DIMENSIONS->value)->nullable();
            $table->string(ItemProperty::LENGTH->value)->nullable();
            $table->string(ItemProperty::WIDTH->value)->nullable();
            $table->string(ItemProperty::HEIGHT->value)->nullable();
            $table->string(ItemProperty::DIAMETER->value)->nullable();
            $table->string(ItemProperty::MATERIAL->value)->nullable();
            $table->string(ItemProperty::SPECIFICATIONS->value)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_products');
    }
};
