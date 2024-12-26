<?php

namespace App\Repositories;

use App\Models\ListingBrand;

class ListingBrandRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingBrand::class);
    }

    public function getModel(): ListingBrand
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
