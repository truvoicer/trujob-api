<?php
namespace App\Enums\Block;

use App\Enums\BlockType;
use App\Services\Block\Type\ListingsBlockType;

enum BlockTypeClass: string
{
    case LISTINGS_BLOCK = ListingsBlockType::class;

    static public function getBlockTypeClass(BlockType $blockType): BlockTypeClass|null
    {
        return match ($blockType) {
            BlockType::LISTINGS_GRID => BlockTypeClass::LISTINGS_BLOCK,
            default => null,
        };
    }
}
