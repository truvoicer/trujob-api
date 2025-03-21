<?php
namespace App\Enums\Pagination;

enum PaginationType: string
{
    case Page = 'page';
    case InfiniteScroll = 'infinite-scroll';
}
