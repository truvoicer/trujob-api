<?php

namespace App\Enums\Listing\Type\Item;

enum ItemProperty: string
{
    case CONDITION = 'condition';
    case MODEL = 'model';
    case YEAR = 'year';
    case COLOR = 'color';
    case SIZE = 'size';
    case DIMENSIONS = 'dimensions';
    case WEIGHT = 'weight';
    case LENGTH = 'length';
    case WIDTH = 'width';
    case HEIGHT = 'height';
    case DIAMETER = 'diameter';
    case VOLUME = 'volume';
    case MATERIAL = 'material';
    case SPECIFICATIONS = 'specifications';
}