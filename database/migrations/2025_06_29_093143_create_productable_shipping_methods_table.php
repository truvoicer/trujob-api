<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productable_shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->morphs('productable', 'productable_type_index');
            $table->foreignId('shipping_method_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productable_shipping_methods');
    }
};
