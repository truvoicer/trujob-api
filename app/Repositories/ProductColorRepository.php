<?php

namespace App\Repositories;

use App\Models\ProductColor;

class ProductColorRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductColor::class);
    }

    public function getModel(): ProductColor
    {
        return parent::getModel();
    }

    public function findByParams(string $sort, string  $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function findByQuery($query)
    {
        return $this->findAll();
    }

}
