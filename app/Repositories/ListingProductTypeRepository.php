<?php

namespace App\Repositories;

use App\Models\ListingProductType;

class ListingProductTypeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingProductType::class);
    }

    public function getModel(): ListingProductType
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
