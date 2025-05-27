<?php

namespace App\Repositories;

use App\Models\ShippingRestriction;

class ShippingRestrictionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ShippingRestriction::class);
    }

    public function getModel(): ShippingRestriction
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
