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
        Schema::create('page_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->foreignId('block_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('background_image')->nullable();
            $table->string('background_color')->nullable();
            $table->boolean('pagination')->nullable()->default(false);
            $table->string('pagination_type')->nullable()->default('page');
            $table->string('pagination_scroll_type')->nullable()->default('block');
            $table->text('content')->nullable();
            $table->integer('order')->default(0);
            $table->json('sidebar_widgets')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
    }
};
