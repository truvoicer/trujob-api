<?php

use App\Enums\Price\PriceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete('cascade');
            $table->enum(
                'price_type',
                array_map(fn(PriceType $type) => $type->value, PriceType::cases())
            );
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->boolean('is_active')->default(false);
            $table->decimal('amount', 19, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
};
