<?php
namespace App\Enums\Block;

use App\Enums\Block\PageBlockType;
use App\Services\Block\Type\ListingsPageBlockType;

enum PageBlockTypeClass: string
{
    case LISTINGS_BLOCK = ListingsPageBlockType::class;

    static public function getPagePageBlockTypeClass(PageBlockType $blockType): PageBlockTypeClass|null
    {
        return match ($blockType) {
            PageBlockType::LISTINGS_GRID => PageBlockTypeClass::LISTINGS_BLOCK,
            default => null,
        };
    }
}
