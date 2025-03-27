<?php

namespace App\Enums\Listing;

enum ListingFetchProperty : string
{
    case QUERY = 'query';
    case ID = 'id';
    case TITLE = 'title';
    case DESCRIPTION = 'description';
    case PRICE = 'price';
    case CREATED_AT = 'created_at';
    case UPDATED_AT = 'updated_at';
    case TYPE = 'type';
    case CATEGORIES = 'categories';
    case USER = 'user';
    case IMAGES = 'images';
    case VIEWS = 'views';
    case STATUS = 'status';
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case COUNTRY = 'country';
    case CITY = 'city';
    case PROXIMITY = 'proximity';
}