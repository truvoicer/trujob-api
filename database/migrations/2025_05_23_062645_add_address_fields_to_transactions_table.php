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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('billing_address_id')
                ->constrained('addresses');
            $table->foreignId('shipping_address_id')
                ->constrained('addresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['billing_address_id']);
            $table->dropColumn('billing_address_id');
            $table->dropForeign(['shipping_address_id']);
            $table->dropColumn('shipping_address_id');
        });
    }
};
