<?php

namespace App\Services\Block\Type;

class ListingsBlockType extends BlockTypeBase
{
   
    public function buildBlockData(array $data): array
    {
        dd($data);
        return [];
    }
}
