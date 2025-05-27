<?php

namespace App\Repositories;

use App\Models\ShippingZone;

class ShippingZoneRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(ShippingZone::class);
    }

    public function getModel(): ShippingZone
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
