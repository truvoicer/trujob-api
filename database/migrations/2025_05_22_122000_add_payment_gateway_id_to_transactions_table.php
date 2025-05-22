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
            
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');

            $table->foreignId('payment_gateway_id')
                ->constrained('payment_gateways')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_gateway_id']);
            $table->dropColumn('payment_gateway_id');

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();
        });
    }
};
