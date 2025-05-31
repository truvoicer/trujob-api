<?php

namespace App\Repositories;

use App\Models\ProductFollow;

class ProductFollowRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ProductFollow::class);
    }

    public function getModel(): ProductFollow
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
