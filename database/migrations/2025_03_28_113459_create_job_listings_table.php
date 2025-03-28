<?php

use App\Enums\Listing\Type\Job\JobProperty;
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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->text(JobProperty::SALARY->value)->nullable();
            $table->text(JobProperty::SALARY_TYPE->value)->nullable();
            $table->text(JobProperty::REQUIREMENTS->value)->nullable();
            $table->text(JobProperty::EXPERIENCE->value)->nullable();
            $table->text(JobProperty::EDUCATION->value)->nullable();
            $table->text(JobProperty::COMPANIES->value)->nullable();
            $table->text(JobProperty::EMPLOYMENT_TYPE->value)->nullable();
            $table->text(JobProperty::JOB_TYPES->value)->nullable();
            $table->text(JobProperty::JOB_CATEGORIES->value)->nullable();
            $table->text(JobProperty::JOB_LEVELS->value)->nullable();
            $table->text(JobProperty::JOB_FUNCTIONS->value)->nullable();
            $table->text(JobProperty::JOB_INDUSTRIES->value)->nullable();
            $table->text(JobProperty::JOB_ROLES->value)->nullable();
            $table->text(JobProperty::JOB_SKILLS->value)->nullable();
            $table->text(JobProperty::JOB_LANGUAGES->value)->nullable();
            $table->text(JobProperty::JOB_CERTIFICATIONS->value)->nullable();
            $table->text(JobProperty::JOB_BENEFITS->value)->nullable();
            $table->text(JobProperty::JOB_PERKS->value)->nullable();
            $table->integer(JobProperty::JOB_APPLICATIONS->value)->nullable();
            $table->text(JobProperty::JOB_APPLICATION_STATUSES->value)->nullable();
            $table->text(JobProperty::JOB_APPLICATION_TYPES->value)->nullable();
            $table->text(JobProperty::JOB_APPLICATION_SOURCES->value)->nullable();
            $table->text(JobProperty::JOB_APPLICATION_STAGES->value)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
