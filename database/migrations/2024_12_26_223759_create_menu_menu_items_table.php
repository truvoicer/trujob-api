
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
        Schema::table('menu_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->after('id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('menu_item_id')->after('menu_id')->constrained('menu_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_menu_items');
    }
};
