<?php

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
            $table->string('name', 50)->comment('E.g., "UK Standard VAT", "Alcohol Excise Duty"');

            $table->enum('type', array_map(fn(TaxRateType $type) => $type->value, TaxRateType::cases()))
                ->comment('Type of tax rate');

            $table->boolean('fixed_rate')
                ->default(false)
                ->comment('Indicates if the tax rate is a fixed amount rather than a percentage');

            $table->decimal('amount', 19, 4)->nullable()
                ->comment('Fixed amount for the tax rate, applicable if fixed_rate is true');

            $table->decimal('rate', 5, 2)
                ->nullable()
                ->comment('Percentage (e.g., 20.00 for 20%)');

            $table->foreignId('country_id')
                ->constrained('countries')
                ->comment('Country');

            $table->boolean('is_active')
                ->default(true)
                ->comment('Indicates if the tax rate is currently active');

            $table->boolean('is_default')
                ->default(false)
                ->comment('Default rate for the country/region');

            $table->enum('scope', array_map(fn(TaxScope $type) => $type->value, TaxScope::cases()))
                ->comment('Tax scope');

            $table->index(['country_id', 'is_active'], 'idx_active_rates')
                ->comment('Index for active tax rates by country code');

            $table->timestamps();
            $table->unique(['country_id', 'type'], 'uq_country_type')
                ->comment('Prevent duplicate tax types for the same country');
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
