<?php

namespace App\Enums\Product\Type\Course;

enum CourseProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case START_DATE = 'start_date';
    case END_DATE = 'end_date';
    case START_TIME = 'start_time';
    case END_TIME = 'end_time';
    case CATEGORIES = 'categories';
    case REQUIREMENTS = 'requirements';
    case LEVEL = 'level';
    case DURATION = 'duration';
    case LANGUAGE = 'language';
    case MAXIMUM_STUDENTS = 'maximum_students';
    case MINIMUM_STUDENTS = 'minimum_students';
    case STUDENTS = 'students';
    case TEACHER = 'teacher';
    case TEACHERS = 'teachers';
}