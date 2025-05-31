<?php

namespace App\Repositories;

use App\Models\ProductBrand;

class ProductBrandRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductBrand::class);
    }

    public function getModel(): ProductBrand
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
