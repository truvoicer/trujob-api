<?php

namespace App\Enums\Listing\Type\Job;

enum JobProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case SALARY = 'salary';
    case CATEGORIES = 'categories';
    case REQUIREMENTS = 'requirements';
    case TYPE = 'type';
    case EXPERIENCE = 'experience';
    case EDUCATION = 'education';
    case COMPANY = 'company';
    case COMPANIES = 'companies';
    case EMPLOYMENT_TYPE = 'employment_type';
    case JOB_TYPE = 'job_type';
    case JOB_TYPES = 'job_types';
    case JOB_CATEGORY = 'job_category';
    case JOB_CATEGORIES = 'job_categories';
    case JOB_SUB_CATEGORY = 'job_sub_category';
    case JOB_SUB_CATEGORIES = 'job_sub_categories';
    case JOB_LEVEL = 'job_level';
    case JOB_LEVELS = 'job_levels';
    case JOB_FUNCTION = 'job_function';
    case JOB_FUNCTIONS = 'job_functions';
    case JOB_INDUSTRY = 'job_industry';
    case JOB_INDUSTRIES = 'job_industries';
    case JOB_ROLE = 'job_role';
    case JOB_ROLES = 'job_roles';
    case JOB_SKILL = 'job_skill';
    case JOB_SKILLS = 'job_skills';
    case JOB_LANGUAGE = 'job_language';
    case JOB_LANGUAGES = 'job_languages';
    case JOB_CERTIFICATION = 'job_certification';
    case JOB_CERTIFICATIONS = 'job_certifications';
    case JOB_BENEFIT = 'job_benefit';
    case JOB_BENEFITS = 'job_benefits';
    case JOB_PERK = 'job_perk';
    case JOB_PERKS = 'job_perks';
    case JOB_APPLICATION = 'job_application';
    case JOB_APPLICATIONS = 'job_applications';
    case JOB_APPLICATION_STATUS = 'job_application_status';
    case JOB_APPLICATION_STATUSES = 'job_application_statuses';
    case JOB_APPLICATION_TYPE = 'job_application_type';
    case JOB_APPLICATION_TYPES = 'job_application_types';
    case JOB_APPLICATION_SOURCE = 'job_application_source';
    case JOB_APPLICATION_SOURCES = 'job_application_sources';
    case JOB_APPLICATION_STAGE = 'job_application_stage';
    case JOB_APPLICATION_STAGES = 'job_application_stages';
    case JOB_APPLICATION_STAGE_STATUS = 'job_application_stage_status';
}