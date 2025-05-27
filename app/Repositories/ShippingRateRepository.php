<?php

namespace App\Repositories;

use App\Models\ShippingRate;

class ShippingRateRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ShippingRate::class);
    }

    public function getModel(): ShippingRate
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
