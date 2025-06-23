<?php

use App\Enums\Order\Discount\DiscountAmountType;
use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use App\Models\Discount;
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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->unique();

            $table->text('description')->nullable();
            $table->enum('type', array_map(fn(DiscountType $type) => $type->value, DiscountType::cases()))->default(DiscountType::CUSTOM->value);
            $table->enum('amount_type', array_map(fn(DiscountAmountType $type) => $type->value, DiscountAmountType::cases()));

            $table->decimal('amount', 19, 4)->nullable();

            $table->decimal('rate', 5, 2)
                ->nullable();

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();

            // Date controls
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('is_active')->default(true);

            // Usage limits
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedInteger('per_user_limit')->nullable();

            // Minimum requirements
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->unsignedInteger('min_items_quantity')->nullable();

            // Applicability
            $table->enum(
                'scope',
                array_map(fn(DiscountScope $scope) => $scope->value, DiscountScope::cases())
            );

            // Code-based discounts
            $table->string('code', 32)->nullable();
            $table->boolean('is_code_required')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'starts_at', 'ends_at']);
            $table->index(['code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
