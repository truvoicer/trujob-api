<?php

namespace App\Enums\Product\Type\RealEstate;

enum RealEstateProperty: string
{
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
    case VIEW = 'view';
    case BALCONY = 'balcony';
    case GARDEN = 'garden';
    case POOL = 'pool';
    case GARAGE = 'garage';
    case BASEMENT = 'basement';
    case ROOF_TYPE = 'roof_type';
    case LOT_SIZE = 'lot_size';
    case PROPERTY_TAX = 'property_tax';
    case HOA_FEES = 'hoa_fees';
    case ZONING = 'zoning';
}