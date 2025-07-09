<?php

use App\Enums\Payment\PaymentGatewayEnvironment;
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
        Schema::create('payment_gateway_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->enum(
                'environment',
                array_map(
                    fn(PaymentGatewayEnvironment $paymentGatewayEnvironment) => $paymentGatewayEnvironment->value,
                    PaymentGatewayEnvironment::cases()
                )
            )->default(PaymentGatewayEnvironment::SANDBOX->value);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_sites');
    }
};
