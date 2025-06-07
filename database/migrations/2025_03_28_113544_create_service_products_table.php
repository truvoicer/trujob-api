<?php

use App\Enums\Product\Category\Service\ServiceProperty;
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
        Schema::create('service_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string(ServiceProperty::TYPE->value)->nullable();
            $table->string(ServiceProperty::WEBSITE->value)->nullable();
            $table->string(ServiceProperty::PHONE->value)->nullable();
            $table->string(ServiceProperty::EMAIL->value)->nullable();
            $table->string(ServiceProperty::OPENING_HOURS->value)->nullable();
            $table->string(ServiceProperty::CLOSING_HOURS->value)->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_products');
    }
};
