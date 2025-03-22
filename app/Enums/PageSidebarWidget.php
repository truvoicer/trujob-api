<?php
namespace App\Enums;

enum PageSidebarWidget: string
{
    case SEARCH_FILTER = 'search_filter';
    case PROXIMITY_FILTER = 'proximity_filter';
    case CATEGORY_FILTER = 'category_filter';
}
