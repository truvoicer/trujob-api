<?php

namespace App\Services\Block\Type;

use App\Models\PageBlock;
use App\Traits\Products\ProductsTrait;

class ProductsBlockType extends BlockTypeBase
{
   use ProductsTrait;

    public function buildBlockUpdateData(PageBlock $pageBlock, array $data): array
    {
        // $existingProperties = $pageBlock->properties;
        // if (!is_array($existingProperties)) {
        //     $existingProperties = [];
        // }

        // if (!empty($data['properties']) && is_array($data['properties'])) {
        //     $data['properties'] = array_merge($existingProperties, $data['properties']);
        // }
        
        return $data;
    }

    public function buildBlockCreateData(PageBlock $pageBlock, array $data): array
    {
        return $data;
    }
}
