<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('carrier')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('processing_time_days')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'display_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_methods');
    }
};
