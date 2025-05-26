<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipping_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['product', 'category', 'location']);
            $table->unsignedBigInteger('restriction_id');
            $table->enum('action', ['allow', 'deny'])->default('deny');
            $table->timestamps();
            
            $table->index(['type', 'restriction_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_restrictions');
    }
};
