<?php

namespace App\Enums\Product\Type\Job;

enum JobProperty: string
{
    case SALARY = 'salary';
    case SALARY_TYPE = 'salary_type';
    case REQUIREMENTS = 'requirements';
    case EXPERIENCE = 'experience';
    case EDUCATION = 'education';
    case COMPANIES = 'companies';
    case EMPLOYMENT_TYPE = 'employment_type';
    case JOB_TYPES = 'job_types';
    case JOB_CATEGORIES = 'job_categories';
    case JOB_LEVELS = 'job_levels';
    case JOB_FUNCTIONS = 'job_functions';
    case JOB_INDUSTRIES = 'job_industries';
    case JOB_ROLES = 'job_roles';
    case JOB_SKILLS = 'job_skills';
    case JOB_LANGUAGES = 'job_languages';
    case JOB_CERTIFICATIONS = 'job_certifications';
    case JOB_BENEFITS = 'job_benefits';
    case JOB_PERKS = 'job_perks';
    case JOB_APPLICATIONS = 'job_applications';
    case JOB_APPLICATION_STATUSES = 'job_application_statuses';
    case JOB_APPLICATION_TYPES = 'job_application_types';
    case JOB_APPLICATION_SOURCES = 'job_application_sources';
    case JOB_APPLICATION_STAGES = 'job_application_stages';
}