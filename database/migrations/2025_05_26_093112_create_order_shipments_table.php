<?php

use App\Enums\Order\Shipping\OrderShipmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipping_method_id')->constrained();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->string('tracking_number')->nullable();

            $table->enum(
                'status',
                array_map(fn(OrderShipmentStatus $orderShipmentStatus) => $orderShipmentStatus->value, OrderShipmentStatus::cases())
            )->nullable()->default(OrderShipmentStatus::PENDING->value);
            $table->decimal('shipping_cost', 10, 2);
            $table->date('estimated_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->date('ship_date')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tracking_number']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_shipments');
    }
};
