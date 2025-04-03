<?php

namespace App\Enums;

enum PageBlock: string
{
    case ID = 'id';
    case PAGE_ID = 'page_id';
    case BLOCK_ID = 'block_id';
    case TITLE = 'title';
    case SUBTITLE = 'subtitle';
    case BACKGROUND_IMAGE = 'background_image';
    case BACKGROUND_COLOR = 'background_color';
    case PAGINATION = 'pagination';
    case PAGINATION_TYPE = 'pagination_type';
    case PAGINATION_SCROLL_TYPE = 'pagination_scroll_type';
    case PROPERTIES = 'properties';
    case CONTENT = 'content';
    case HAS_SIDEBAR = 'has_sidebar';
    case SIDEBAR_WIDGETS = 'sidebar_widgets';
    case ORDER = 'order';
    case CREATED_AT = 'created_at';
    case UPDATED_AT = 'updated_at';
}
