<?php

use App\Enums\Subscription\SubscriptionSetupFeeFailureAction;
use App\Enums\Subscription\SubscriptionType;
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
        Schema::create('price_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_id')
                  ->constrained('prices')
                  ->onDelete('cascade');

            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->enum(
                'type',
                array_map(
                    fn(SubscriptionType $type) => $type->value,
                    SubscriptionType::cases()
                )
            );
            $table->boolean('has_setup_fee')->default(false);
            $table->decimal('setup_fee_value', 19, 4)->nullable();
            $table->foreignId('setup_fee_currency_id')
                  ->nullable()
                  ->constrained('currencies')
                  ->nullOnDelete();
            $table->boolean('auto_bill_outstanding')->default(true);

            $table->enum(
                'setup_fee_failure_action',
                array_map(
                    fn(SubscriptionSetupFeeFailureAction $action) => $action->value,
                    SubscriptionSetupFeeFailureAction::cases()
                )
            )->default(SubscriptionSetupFeeFailureAction::CANCEL->value);
            $table->integer('payment_failure_threshold')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_subscriptions');
    }
};
