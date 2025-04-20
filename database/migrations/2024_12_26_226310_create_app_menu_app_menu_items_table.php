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
        Schema::create('app_menu_app_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_menu_id')->constrained('app_menus')->onDelete('cascade');
            $table->foreignId('app_menu_item_id')->constrained('app_menu_items')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_menu_app_menu_items');
    }
};
