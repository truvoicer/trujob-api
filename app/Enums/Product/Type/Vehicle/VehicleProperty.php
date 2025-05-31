<?php

namespace App\Enums\Product\Type\Vehicle;

enum VehicleProperty: string
{
    case TYPE = 'type';
    case MAKE = 'make';
    case MODEL = 'model';
    case YEAR = 'year';
    case MILEAGE = 'mileage';
    case FUEL_TYPE = 'fuel_type';
    case TRANSMISSION = 'transmission'; 
    case BODY_TYPE = 'body_type';
    case ENGINE_SIZE = 'engine_size';
    case COLOUR = 'colour';
    case CONDITION = 'condition';
    case VIN = 'vin';
    case PLATE_NUMBER = 'plate_number';
}