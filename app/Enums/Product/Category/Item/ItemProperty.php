<?php

namespace App\Enums\Product\Category\Item;

enum ItemProperty: string
{
    case CONDITION = 'condition';
    case MODEL = 'model';
    case YEAR = 'year';
    case COLOR = 'color';
    case SIZE = 'size';
    case DIMENSIONS = 'dimensions';
    case WEIGHT = 'weight';
    case DEPTH = 'depth';
    case WIDTH = 'width';
    case HEIGHT = 'height';
    case DIAMETER = 'diameter';
    case VOLUME = 'volume';
    case MATERIAL = 'material';
    case SPECIFICATIONS = 'specifications';
}
