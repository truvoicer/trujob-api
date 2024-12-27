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
            $table->enum('view', array_map(fn($type) => $type->value, ViewType::cases()))->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_robots')->nullable();
            $table->string('meta_canonical')->nullable();
            $table->string('meta_author')->nullable();
            $table->string('meta_publisher')->nullable();
            $table->string('meta_og_title')->nullable();
            $table->string('meta_og_description')->nullable();
            $table->string('meta_og_type')->nullable();
            $table->string('meta_og_url')->nullable();
            $table->string('meta_og_image')->nullable();
            $table->string('meta_og_site_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_home')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_protected')->default(false);
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
