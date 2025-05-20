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
        Schema::table('prices', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete('cascade');
            $table->foreignId('price_type_id')->nullable()->constrained('price_types')->nullOnDelete();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(false);
            $table->decimal('amount', 19, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['price_type_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('currency_id');
            $table->dropColumn('country_id');
            $table->dropColumn('price_type_id');
            $table->dropColumn('valid_from');
            $table->dropColumn('valid_to');
            $table->dropColumn('is_default');
            $table->dropColumn('is_active');
        });
    }
};
