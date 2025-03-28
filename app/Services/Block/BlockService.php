<?php

namespace App\Services\Block;

use App\Enums\Block\PagePageBlockTypeClass;
use App\Models\Block;
use App\Models\PageBlock;
use App\Services\BaseService;

class BlockService extends BaseService
{
    public static function getPageBlockTypeInstance(PageBlock $pageBlock) {
        if (! $pageBlock->block) {
            throw new \Exception('PageBlock relation "block" not found');
        }
        $block = $pageBlock->block;
        if (! $block->type) {
            throw new \Exception('Block type not found');
        }
        $blockClass = PagePageBlockTypeClass::getPagePageBlockTypeClass($block->type);
        if (! $blockClass) {
            return false;
        }
        return app($blockClass->value);
    }

    public static function buildBlockUpdateData(PageBlock $pageBlock, array $data): array
    {
        $instance = self::getPageBlockTypeInstance($pageBlock);
        if (! $instance) {
            return $data;
        }
        return $instance->buildBlockUpdateData($pageBlock, $data);
    }

    public static function buildBlockCreateData(PageBlock $pageBlock, array $data): array
    {
        $instance = self::getPageBlockTypeInstance($pageBlock);
        if (! $instance) {
            return $data;
        }
        return $instance->buildBlockCreateData($pageBlock, $data);
    }
}
