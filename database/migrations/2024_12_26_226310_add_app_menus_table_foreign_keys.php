<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_menu_items', function (Blueprint $table) {
            $table->foreignId('app_menu_id')->after('id')->constrained('app_menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_menu_items', function (Blueprint $table) {
            $table->dropForeign(['app_menu_id']);
            $table->dropColumn('app_menu_id');
        });
    }
};
