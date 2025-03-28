<?php
namespace App\Enums\Listing;

enum ListingBlockSidebarWidget: string
{
    case SEARCH_FILTER = 'search_filter';
    case PROXIMITY_FILTER = 'proximity_filter';
    case CATEGORY_FILTER = 'category_filter';
}
