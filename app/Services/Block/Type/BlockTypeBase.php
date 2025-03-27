<?php

namespace App\Services\Block\Type;

use App\Services\BaseService;

abstract class BlockTypeBase extends BaseService
{
   
    abstract public function buildBlockData(array $data): array; 
    
}
