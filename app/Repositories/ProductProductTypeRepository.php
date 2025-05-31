<?php

namespace App\Repositories;

use App\Models\ProductProductType;

class ProductProductTypeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductProductType::class);
    }

    public function getModel(): ProductProductType
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
