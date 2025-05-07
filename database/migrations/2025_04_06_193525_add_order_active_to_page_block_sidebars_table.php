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
        Schema::table('page_block_sidebars', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('sidebar_id');
            $table->boolean('active')->default(true)->after('order');
        });
        Schema::table('page_sidebars', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('sidebar_id');
            $table->boolean('active')->default(true)->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_block_sidebars', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('active');
        });
        Schema::table('page_sidebars', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('active');
        });
    }
};
