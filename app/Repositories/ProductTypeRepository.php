<?php

namespace App\Repositories;

use App\Models\ProductType;

class ProductTypeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductType::class);
    }

    public function getModel(): ProductType
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
