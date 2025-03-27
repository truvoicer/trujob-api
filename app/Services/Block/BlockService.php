<?php

namespace App\Services\Block;

use App\Enums\Block\BlockTypeClass;
use App\Models\Block;
use App\Services\BaseService;

class BlockService extends BaseService
{
   public static function buildBlockData(Block $block, array $data): array
   {
        if (! $block->type) {
            return [];
        }
        $blockClass = BlockTypeClass::getBlockTypeClass($block->type);
        if (! $blockClass) {
            return [];
        }
        $instance = app($blockClass->value);
        return $instance->buildBlockData($data);
   }
}
