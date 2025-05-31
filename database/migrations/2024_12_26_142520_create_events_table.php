<?php

use App\Enums\Product\Type\Event\EventProperty;
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
        Schema::create('event_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId( 'product_id')->constrained('products')->onDelete('cascade');
            $table->text(EventProperty::NOTES->value)->nullable();
            $table->string(EventProperty::LOCATION->value)->nullable();
            $table->string(EventProperty::LATITUDE->value)->nullable();
            $table->string(EventProperty::LONGITUDE->value)->nullable();
            $table->dateTime(EventProperty::START_DATE->value)->nullable();
            $table->dateTime(EventProperty::END_DATE->value)->nullable();
            $table->string(EventProperty::STATUS->value)->nullable();
            $table->boolean(EventProperty::IS_PUBLIC->value)->default(false);
            $table->boolean(EventProperty::IS_ALL_DAY->value)->default(false);
            $table->boolean(EventProperty::IS_RECURRING->value)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
