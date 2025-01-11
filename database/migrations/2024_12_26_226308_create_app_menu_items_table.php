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
        Schema::create('app_menu_items', function (Blueprint $table) {
            $table->id();
            $table->boolean('initial_screen')->default(false);
            $table->string('label')->nullable();
            $table->string('screen')->nullable();
            $table->string('type')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('show_in_menu')->nullable();
            $table->string('icon')->nullable();
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
        Schema::dropIfExists('app_menu_items');
    }
};
