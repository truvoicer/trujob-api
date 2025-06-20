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
        Schema::create('shipping_zone_ables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')
                ->constrained('shipping_zones')
                ->cascadeOnDelete();
            $table->morphs('shipping_zoneable', 'shipping_zoneable_type_index');

            $table->unique(
                [
                    'shipping_zone_id',
                    'shipping_zoneable_id',
                    'shipping_zoneable_type'
                ],
                'shipping_zone_ables_unique'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_ables');
    }
};
