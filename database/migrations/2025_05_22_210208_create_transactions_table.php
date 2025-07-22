<?php

use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
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
        Schema::create('transactions', function (Blueprint $table) {

            $table->id();
            // Foreign key to the orders table
            $table->foreignId('order_id')
                ->constrained('orders');

            $table->foreignId('payment_gateway_id')
                ->constrained('payment_gateways');

            // Currency of the transaction
            $table->string('currency_code', 3);

            // Unique transaction ID provided by the payment gateway
            $table->string('transaction_id')->unique()->nullable();

            // Status of the payment (e.g., 'pending', 'completed', 'failed', 'refunded')
            $table->enum(
                'status',
                array_map(
                    fn(TransactionStatus $status) => $status->value,
                    TransactionStatus::cases()
                )
            );
            $table->enum(
                'payment_status',
                array_map(
                    fn(TransactionPaymentStatus $status) => $status->value,
                    TransactionPaymentStatus::cases()
                )
            );

            // Amount processed by the payment gateway (can be different from order total due to partial payments, refunds)
            $table->decimal('amount', 10, 2)->nullable();

            // JSON column to store order-specific data (e.g., order details, items purchased)
            $table->json('order_data')->nullable();

            // JSON column to store gateway-specific data (e.g., PayPal approval links, Stripe charge IDs, metadata)
            $table->json('transaction_data')->nullable();

            // Timestamps for creation and last update
            $table->timestamps();

            // Index for faster lookups by gateway name and status
            $table->index([
                'payment_gateway_id',
                'status',
                'order_id',
            ], 'transactions_user_order_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
