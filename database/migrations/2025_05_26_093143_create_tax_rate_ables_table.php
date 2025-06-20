<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tax_rate_ables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rate_id')->constrained()->cascadeOnDelete();
            $table->morphs('tax_rateable', 'tax_rateable_type_index');
            $table->timestamps();

            $table->index(
                [
                    'tax_rate_id',
                    'tax_rateable_id',
                    'tax_rateable_type'
                ],
                'tax_rate_tax_rateable_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_rate_ables');
    }
};
