<?php

namespace App\Services\Block\Type;

use App\Models\PageBlock;
use App\Services\BaseService;

abstract class BlockTypeBase extends BaseService
{
   
    abstract public function buildBlockUpdateData(PageBlock $pageBlock, array $data): array; 
    abstract public function buildBlockCreateData(PageBlock $pageBlock, array $data): array; 
    
}
