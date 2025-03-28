<?php

namespace App\Enums\Listing\Type\Food;

enum FoodProperty: string
{
    case DIETARY_REQUIREMENTS = 'dietary_requirements';
    case ALLERGENS = 'allergens';
    case INGREDIENTS = 'ingredients';
    case NUTRITIONAL_INFORMATION = 'nutritional_information';
    case SERVING_SIZE = 'serving_size';
    case PACKAGING = 'packaging';
    case STORAGE = 'storage';
    case STORAGE_REQUIREMENTS = 'storage_requirements';
    case HEALTH_AND_SAFETY = 'health_and_safety';
    case EXPIRY_DATE = 'expiry_date';
    case USE_BY_DATE = 'use_by_date';
    case SELL_BY_DATE = 'sell_by_date';
    case MANUFACTURED_DATE = 'manufactured_date';
}