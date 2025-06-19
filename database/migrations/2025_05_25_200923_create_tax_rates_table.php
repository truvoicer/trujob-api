<?php

use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Order\Tax\TaxScope;
use App\Enums\Order\Tax\TaxRateType;
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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()
                ->comment('E.g., "UK Standard VAT", "Alcohol Excise Duty"');
            $table->string('label')->unique();

            $table->enum('type', array_map(fn(TaxRateType $type) => $type->value, TaxRateType::cases()))
                ->comment('Type of tax rate');

            $table->enum('amount_type', array_map(fn(TaxRateAmountType $type) => $type->value, TaxRateAmountType::cases()))
                ->comment('Amount type of tax rate');

            $table->decimal('amount', 19, 4)->nullable()
                ->comment('Fixed amount for the tax rate, applicable if fixed_rate is true');

            $table->decimal('rate', 5, 2)
                ->nullable()
                ->comment('Percentage (e.g., 20.00 for 20%)');

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->comment('Currency for the tax rate, used if fixed_rate is true');

            $table->boolean('is_active')
                ->default(true)
                ->comment('Indicates if the tax rate is currently active');

            $table->enum('scope', array_map(fn(TaxScope $type) => $type->value, TaxScope::cases()))
                ->comment('Tax scope');

            $table->index(['type', 'is_active', 'amount_type', 'name'], 'idx_active_rates')
                ->comment('Index for active tax rates by country code');

            $table->timestamps();
            // $table->unique(['country_id', 'type'], 'uq_country_type')
            //     ->comment('Prevent duplicate tax types for the same country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
