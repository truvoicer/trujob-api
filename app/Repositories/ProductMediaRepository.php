<?php

namespace App\Repositories;

use App\Models\ProductMedia;

class ProductMediaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductMedia::class);
    }

    public function getModel(): ProductMedia
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
