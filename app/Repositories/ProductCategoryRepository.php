<?php

namespace App\Repositories;

use App\Models\ProductCategory;

class ProductCategoryRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductCategory::class);
    }

    public function getModel(): ProductCategory
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
