<?php

namespace App\Enums\Listing\Type\Pet;

enum PetProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case PET_TYPE = 'pet_type';
    case BREED = 'breed';
}