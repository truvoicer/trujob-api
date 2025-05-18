<?php

namespace App\Repositories;

use App\Models\Price;

class PriceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Price::class);
    }

    public function getModel(): Price
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
