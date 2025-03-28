<?php

namespace App\Enums\Listing\Type\RealEstate;

enum RealEstateProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case PRICE = 'price';
    case BEDROOMS = 'bedrooms';
    case BATHROOMS = 'bathrooms';
    case SIZE = 'size';
    case TYPE = 'type';
    case FURNISHED = 'furnished';
    case PARKING = 'parking';
    case PETS_ALLOWED = 'pets_allowed';
    case SMOKE_ALLOWED = 'smoke_allowed';
    case CONSTRUCTION_YEAR = 'construction_year';
    case ENERGY_EFFICIENCY = 'energy_efficiency';
    case HEATING = 'heating';
    case COOLING = 'cooling';
    case FLOORING = 'flooring';
    case AMENITIES = 'amenities';
    case IMAGES = 'images';
    case VIDEOS = 'videos';
    case DOCUMENTS = 'documents';
    case CATEGORIES = 'categories';
    case FEATURES = 'features';
    case TAGS = 'tags';
    case STATUS = 'status';
}