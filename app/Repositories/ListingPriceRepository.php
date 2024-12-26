<?php

namespace App\Repositories;

use App\Models\ListingPrice;

class ListingPriceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ListingPrice::class);
    }

    public function getModel(): ListingPrice
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
