<?php

namespace App\Repositories;

use App\Models\ProductProductCategory;

class ProductProductCategoryRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductProductCategory::class);
    }

    public function getModel(): ProductProductCategory
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
