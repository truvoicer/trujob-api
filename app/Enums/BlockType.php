<?php
namespace App\Enums;

enum BlockType: string
{
    case HERO = 'hero';
    case FEATURED = 'featured';
    case ICON_GRID = 'icon_grid';
    case LISTINGS_GRID = 'listings_grid';
}
