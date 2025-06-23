<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('discountables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')
                ->constrained('discounts')
                ->cascadeOnDelete()
                ->comment('Foreign key to the discounts table');
            $table->morphs('discountable', 'discountable_type_index');
            $table->timestamps();

            $table->softDeletes();
            $table->index(
                [
                    'discount_id',
                    'discountable_id',
                    'discountable_type'
                ],
                'discount_discountable_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('discountables');
    }
};
