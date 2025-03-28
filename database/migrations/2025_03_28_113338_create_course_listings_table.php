<?php

use App\Enums\Listing\Type\Course\CourseProperty;
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
        Schema::create('course_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string(CourseProperty::LOCATION->value)->nullable();
            $table->string(CourseProperty::LATITUDE->value)->nullable();
            $table->string(CourseProperty::LONGITUDE->value)->nullable();
            $table->date(CourseProperty::START_DATE->value)->nullable();
            $table->date(CourseProperty::END_DATE->value)->nullable();
            $table->time(CourseProperty::START_TIME->value)->nullable();
            $table->time(CourseProperty::END_TIME->value)->nullable();
            $table->json(CourseProperty::CATEGORIES->value)->nullable();
            $table->json(CourseProperty::REQUIREMENTS->value)->nullable();
            $table->string(CourseProperty::LEVEL->value)->nullable();
            $table->string(CourseProperty::DURATION->value)->nullable();
            $table->string(CourseProperty::LANGUAGE->value)->nullable();
            $table->integer(CourseProperty::MAXIMUM_STUDENTS->value)->nullabl();
            $table->integer(CourseProperty::MINIMUM_STUDENTS->value)->nullabl();
            $table->json(CourseProperty::STUDENTS->value)->nullable();
            $table->json(CourseProperty::TEACHER->value)->nullable();
            $table->json(CourseProperty::TEACHERS->value)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_listings');
    }
};
