<?php

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
        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->string('weight_unit')->default('kg')->after('max_weight');
            $table->string('height_unit')->default('cm')->after('max_height');
            $table->string('length_unit')->default('cm')->after('max_length');
            $table->string('width_unit')->default('cm')->after('max_width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
