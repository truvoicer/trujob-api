<?php

namespace App\Repositories;

use App\Models\OrderShipment;

class OrderShipmentRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(OrderShipment::class);
    }

    public function getModel(): OrderShipment
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
