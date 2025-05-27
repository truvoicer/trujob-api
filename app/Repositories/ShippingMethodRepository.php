<?php

namespace App\Repositories;

use App\Models\ShippingMethod;

class ShippingMethodRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ShippingMethod::class);
    }

    public function getModel(): ShippingMethod
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
