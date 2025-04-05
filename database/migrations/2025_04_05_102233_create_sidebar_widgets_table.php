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
        Schema::create('sidebar_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sidebar_id')->constrained('sidebars')->onDelete('cascade');
            $table->foreignId('widget_id')->constrained('widgets')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->nullable()->default(0);
            $table->boolean('has_container')->nullable()->default(false);
            $table->string('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_widgets');
    }
};
