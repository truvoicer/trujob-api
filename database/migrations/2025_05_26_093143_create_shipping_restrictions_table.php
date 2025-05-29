<?php

use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Enums\Order\Shipping\ShippingRestrictionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipping_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('restrictionable_id');
            $table->string('restrictionable_type');
            $table->enum(
                'action', 
                array_map(
                    fn(ShippingRestrictionAction $type) => $type->value,
                    ShippingRestrictionAction::cases()
                )
            );
            $table->timestamps();

            $table->index(['restrictionable_id', 'restrictionable_type'], 'shipping_restrictionable_type_id_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_restrictions');
    }
};
