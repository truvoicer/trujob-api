<?php

namespace App\Enums\Listing\Type\Vehicle;

enum VehicleProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case MAKE = 'make';
    case MODEL = 'model';
    case YEAR = 'year';
    case MILEAGE = 'mileage';
    case FUEL_TYPE = 'fuel_type';
    case TRANSMISSION = 'transmission'; 
    case BODY_TYPE = 'body_type';
    case ENGINE_SIZE = 'engine_size';
    case COLOUR = 'colour';
    case PRICE = 'price';   
    case IMAGES = 'images';
    case VIDEOS = 'videos';
    case DOCUMENTS = 'documents';
    case CATEGORIES = 'categories';
    case FEATURES = 'features';
    case TAGS = 'tags';
    case STATUS = 'status';
    case EXPIRES_AT = 'expires_at';
}