<?php

namespace App\Enums\Listing\Type\Food;

enum FoodProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case CATEGORIES = 'categories';
    case DIETARY_REQUIREMENTS = 'dietary_requirements';
    case ALLERGENS = 'allergens';
    case INGREDIENTS = 'ingredients';
    case NUTRITIONAL_INFORMATION = 'nutritional_information';
    case PACKAGING = 'packaging';
    case STORAGE = 'storage';
    case EXPIRY_DATE = 'expiry_date';
    case MANUFACTURED_DATE = 'manufactured_date';
    case BRAND = 'brand';
    case WEIGHT = 'weight';
    case PRICE = 'price';
    case QUANTITY = 'quantity';
    case UNIT = 'unit';
    case IMAGES = 'images';
    case VIDEOS = 'videos';
    case REVIEWS = 'reviews';
    case RATING = 'rating';
    case SELLER = 'seller';
    case SELLERS = 'sellers';
    case BUYER = 'buyer';
    case BUYERS = 'buyers';
    case STATUS = 'status';
    case TYPE = 'type';
}