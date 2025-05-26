<?php

namespace App\Repositories;

use App\Models\Discount;

class DiscountRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Discount::class);
    }

    public function getModel(): Discount
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
