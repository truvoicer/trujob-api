<?php
namespace App\Enums\Block;

use App\Enums\Block\BlockType;
use App\Services\Block\Type\ProductsBlockType;

enum BlockTypeClass: string
{
    case PRODUCTS_BLOCK = ProductsBlockType::class;

    static public function getBlockTypeClass(BlockType $blockType): BlockTypeClass|null
    {
        return match ($blockType) {
            BlockType::PRODUCTS_GRID => BlockTypeClass::PRODUCTS_BLOCK,
            default => null,
        };
    }
}
