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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->after('id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('url')->nullable();
            $table->string('target')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('menu_items');
    }
};
