<?php

use App\Enums\ViewType;
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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('view')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_home')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_protected')->default(false);
            $table->boolean('has_sidebar')->nullable()->default(false);
            $table->json('sidebar_widgets')->nullable();
            $table->json('settings')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
